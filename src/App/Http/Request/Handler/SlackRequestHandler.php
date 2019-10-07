<?php

declare(strict_types=1);

namespace App\Http\Request\Handler;

use App\Slack\Messages\SlackMentionMessage;
use App\Slack\Messenger as Slack;
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
    private LoggerInterface $logger;

    public function __construct(AI $ai, Slack $slack, LoggerInterface $logger)
    {
        $this->ai = $ai;
        $this->slack = $slack;
        $this->logger = $logger;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $this->logger->debug('Request incoming: ' . str($request));
            $requestBody = json_decode((string)$request->getBody(), true);
            $type = $requestBody['type'] ?? 'not_set';

            switch ($type) {
                case 'url_verification':
                    $this->logger->info('URL Verification request detected, sending challenge');
                    return new JsonResponse(['challenge' => $requestBody['challenge']]);
                case 'app_mention':
                    $this->logger->info('Event app_mention request detected, processing it');
                    $slackMessage = SlackMentionMessage::createFromArray($requestBody);
                    $deployParameters = $this->ai->recognizeFromSlackMessage($slackMessage);
                    $this->slack->sendConfirmationMessage($deployParameters);
                    $this->logger->info('Finished processing app_mention', [
                        'branch' => $deployParameters->getBranch(),
                        'market' => $deployParameters->getMarket(),
                        'environment' => $deployParameters->getEnvironment(),
                    ]);

                    return new JsonResponse([
                        'branch' => $deployParameters->getBranch(),
                        'environment' => $deployParameters->getEnvironment(),
                        'market' => $deployParameters->getMarket(),
                    ]);
                default:
                    $this->logger->info('Non present or invalid type on request');
                    return new JsonResponse(['ack' => time()]);
            }
        } catch (\Exception $exception) {
            $exceptionArray = [
                'exception_class' => get_class($exception),
                'error' => $exception->getMessage(),
                'line' => $exception->getLine(),
                'file' => $exception->getFile(),
                'code' => $exception->getCode(),
                'previous' => $exception->getPrevious(),
                'trace_as_string' => $exception->getTraceAsString(),
                'trace' => $exception->getTrace(),
            ];

            $this->logger->error($exception->getMessage(), $exceptionArray);

            return new JsonResponse($exceptionArray);
        }
    }
}
