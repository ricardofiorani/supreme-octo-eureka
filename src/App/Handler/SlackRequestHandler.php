<?php

declare(strict_types=1);

namespace App\Handler;

use App\Messages\SlackMentionMessage;
use App\WitAI\Adapter as AI;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;

use function time;

class SlackRequestHandler implements RequestHandlerInterface
{
    private AI $ai;

    public function __construct(AI $ai)
    {
        $this->ai = $ai;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $requestBody = json_decode((string)$request->getBody(), true);
        $type = $requestBody['type'] ?? 'not_set';

        switch ($type) {
            case 'url_verification':
                return new JsonResponse(['challenge' => $requestBody['challenge']]);
            case 'app_mention':
                $slackMessage = SlackMentionMessage::createFromArray($requestBody);
                $deployParameters = $this->ai->recognizeFromSlackMessage($slackMessage);

                return new JsonResponse([
                    'branch' => $deployParameters->getBranch(),
                    'environment' => $deployParameters->getEnvironment(),
                    'market' => $deployParameters->getMarket(),
                ]);
            default:
                return new JsonResponse(['ack' => time()]);
        }
    }
}
