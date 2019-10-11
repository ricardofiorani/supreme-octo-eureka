<?php declare(strict_types=1);

namespace App\Slack;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;
use Zend\Diactoros\Request;
use Zend\Diactoros\Uri;
use function GuzzleHttp\Psr7\str;

class Messenger
{
    private ClientInterface $httpClient;
    private LoggerInterface $logger;

    public function __construct(ClientInterface $httpClient, LoggerInterface $logger)
    {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }

    public function sendMessage(string $message): void
    {
        $uri = new Uri('https://slack.com/api/chat.postMessage');
        $authorization = sprintf('Bearer %s', getenv('SLACK_TOKEN'));
        $request = (new Request())
            ->withAddedHeader('Authorization', $authorization)
            ->withAddedHeader('Content-Type', 'application/json')
            ->withAddedHeader('Accept', '*/*')
            ->withUri($uri)
            ->withMethod('POST');
        $request->getBody()->write(json_encode([
            'token' => getenv('SLACK_TOKEN'),
            'channel' => getenv('SLACK_CHANNEL'),
            'text' => $message,
        ]));

        try {
            $response = $this->httpClient->sendRequest($request);
            $this->logger->info('Request sent to Slack API', [
                'request' => str($request),
                'response' => str($response),
            ]);
        } catch (ClientExceptionInterface $exception) {
            throw new \LogicException(
                sprintf('Failed to send Slack message. Reason: %s', $exception->getMessage()),
                0,
                $exception
            );
        }
    }
}
