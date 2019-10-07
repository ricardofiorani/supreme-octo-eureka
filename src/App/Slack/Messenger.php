<?php declare(strict_types=1);

namespace App\Slack;

use App\Jenkins\DeployParameters;
use Psr\Http\Client\ClientInterface;
use Zend\Diactoros\Request;
use Zend\Diactoros\Uri;

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
        $authorization = sprintf('Bearer %s', getenv('WITAI_SERVER_TOKEN'));
        $request = (new Request())
            ->withAddedHeader('Authorization', $authorization)
            ->withAddedHeader('Content-Type', 'application/json')
            ->withUri($uri);
        $request->getBody()->write(json_encode([
            'token' => getenv('SLACK_TOKEN'),
            'channel' => getenv('SLACK_CHANNEL'),
            'text' => $this->buildTextFromParameters($deployParameters),
        ]));

//        var_dump(\GuzzleHttp\Psr7\str($request)); die;



        $this->httpClient->sendRequest($request);
    }

    private function buildTextFromParameters(DeployParameters $deployParameters): string
    {
        return sprintf(
            'Hello %s, I will deploy `%s` to the environment `%s` simulating the market `%s`',
            $deployParameters->getMessage()->getUser(),
            $deployParameters->getBranch(),
            $deployParameters->getEnvironment(),
            $deployParameters->getMarket()
        );
    }
}
