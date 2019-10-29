<?php declare(strict_types=1);

namespace App\Http\Request\Handler;

use App\Domain\Action\ActionParameters;
use App\Service\ActionService;
use App\Slack\Messages\SlackMentionMessage;
use App\Slack\Messenger as Slack;
use App\Slack\Permission\Exception\PermissionException;
use App\Slack\Permission\PermissionChecker;
use App\WitAI\Adapter as AI;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Zend\Diactoros\Response\JsonResponse;

use function GuzzleHttp\Psr7\str;
use function time;

class SlackRequestHandler implements RequestHandlerInterface
{
    private AI $ai;
    private Slack $slack;
    private ActionService $actionRunner;
    private LoggerInterface $logger;
    private PermissionChecker $permissionChecker;

    public function __construct(
        AI $ai,
        Slack $slack,
        ActionService $actionRunner,
        PermissionChecker $userPermission,
        LoggerInterface $logger
    ) {
        $this->ai = $ai;
        $this->slack = $slack;
        $this->actionRunner = $actionRunner;
        $this->permissionChecker = $userPermission;
        $this->logger = $logger;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->logger->debug('Request incoming', ['request' => str($request)]);
        $requestBody = json_decode((string)$request->getBody(), true);
        $type = $requestBody['event']['type'] ?? $requestBody['type'] ?? 'not_set';

        switch ($type) {
            case 'url_verification':
                return $this->verifyUrl($requestBody);
            case 'app_mention':
                return $this->processAppMention($requestBody);
            default:
                $this->logger->info('Non present or invalid type on request');

                return new JsonResponse(['ack' => time()]);
        }
    }

    private function verifyUrl($requestBody): ResponseInterface
    {
        $this->logger->debug('URL Verification request detected, sending challenge');

        return new JsonResponse(['challenge' => $requestBody['challenge']]);
    }

    private function processAppMention($requestBody): ResponseInterface
    {
        $this->logger->debug('Event app_mention request detected, processing it');
        $slackMessage = SlackMentionMessage::createFromArray($requestBody['event']);
        $this->logger->debug('Message detected', ['message' => $slackMessage->getText()]);

        try {
            $this->permissionChecker->checkUser($slackMessage->getUser());
            $this->permissionChecker->checkChannel($slackMessage->getChannel());
            $entities = $this->ai->recognizeEntities($slackMessage->getText());
            $this->logger->debug('Entities recognized', $entities->getEntities()->toArray());
            $actionParameters = new ActionParameters($entities, $slackMessage);
            $actionResponse = $this->actionRunner->process($actionParameters);

            $this->slack->sendMessage(
                $actionResponse->getResponseMessage(),
                $slackMessage->getChannel(),
                $slackMessage->getUser()
            );

            $this->logger->debug('Message sent to slack', ['message' => $actionResponse->getResponseMessage()]);
            $this->logger->debug('Finished processing app_mention request');

            return new JsonResponse([
                'success' => $actionResponse->isSuccessful(),
                'message' => $actionResponse->getResponseMessage(),
                'parameters_used' => $actionResponse->getParametersUsed(),
                'entities' => $entities->toArray(),
            ]);
        } catch (PermissionException $exception) {
            $this->logger->warning($exception->getMessage());

            $this->slack->sendMessage(
                $exception->getMessage(),
                $slackMessage->getChannel(),
                $slackMessage->getUser()
            );

            return new JsonResponse([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        } catch (\Exception $exception) {
            $exceptionArray = [
                'success' => false,
                'exception_class' => get_class($exception),
                'error' => $exception->getMessage(),
                'line' => $exception->getLine(),
                'file' => $exception->getFile(),
                'code' => $exception->getCode(),
                'previous' => $exception->getPrevious(),
                'trace' => $exception->getTrace(),
            ];

            $this->logger->error($exception->getMessage(), $exceptionArray);
            $message = <<<STRING
I tried my best but I failed in my source code :broken_heart: :
> `{$exception->getMessage()}` on `{$exception->getFile()}({$exception->getLine()})`.
STRING;
            $this->slack->sendMessage(
                $message,
                $slackMessage->getChannel(),
                $slackMessage->getUser()
            );
            $this->logger->debug('Finished sending error notification to slack');

            return new JsonResponse($exceptionArray);
        }
    }
}
