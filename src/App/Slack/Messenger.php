<?php declare(strict_types=1);

namespace App\Slack;

use App\Jenkins\DeployParameters;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Zend\Diactoros\Request;
use Zend\Diactoros\Uri;
use function GuzzleHttp\Psr7\str;

class Messenger
{
    private ClientInterface $httpClient;

    public function __construct(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function sendConfirmationMessage(DeployParameters $deployParameters): void
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
            'text' => $this->buildTextFromParameters($deployParameters),
        ]));

        try {
            $this->httpClient->sendRequest($request);
        } catch (ClientExceptionInterface $exception) {
            throw new \LogicException(
                sprintf('Failed to send Slack message. Reason: %s', $exception->getMessage()),
                0,
                $exception
            );
        }
    }

    private function buildTextFromParameters(DeployParameters $deployParameters): string
    {
        return sprintf(
            'Hello @%s, I will deploy `%s` to the environment `%s` simulating the market `%s` :rocket:',
            $deployParameters->getMessage()->getUser(),
            $deployParameters->getBranch(),
            $deployParameters->getEnvironment(),
            $deployParameters->getMarket()
        );
    }
}
