<?php declare(strict_types=1);

namespace App\Http\Request\Handler;

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
        $this->logger->debug('Request incoming: ' . str($request));
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
        $this->logger->info('URL Verification request detected, sending challenge');

        return new JsonResponse(['challenge' => $requestBody['challenge']]);
    }

    private function processAppMention($requestBody): ResponseInterface
    {
        $this->logger->info('Event app_mention request detected, processing it');
        $slackMessage = SlackMentionMessage::createFromArray($requestBody['event']);
        $this->logger->info('Message detected', ['message' => $slackMessage->getText()]);

        try {
            $this->permissionChecker->checkUser($slackMessage->getUser());
            $this->permissionChecker->checkChannel($slackMessage->getChannel());

            $entities = $this->ai->recognizeEntities($slackMessage->getText());
            $actionResponse = $this->actionRunner->processEntities($entities);

            $this->slack->sendMessage(
                $actionResponse->getResponseMessage(),
                $slackMessage->getChannel(),
                $slackMessage->getUser()
            );
            $this->logger->info('Finished sending the notification to slack');


            //In here I would send to Jenkins the build/deploy job request,
            // but since this is just a proof of concept to test how it works on wit.ai and slack,
            //I will not do it so soon

            $this->logger->info('Finished processing app_mention');

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
I tried my best but I failed :broken_heart: :
> `{$exception->getMessage()}` on `{$exception->getFile()}({$exception->getLine()})`.
STRING;
            $this->slack->sendMessage(
                $message,
                $slackMessage->getChannel(),
                $slackMessage->getUser()
            );
            $this->logger->info('Finished sending error notification to slack');

            return new JsonResponse($exceptionArray);
        }
    }
}
