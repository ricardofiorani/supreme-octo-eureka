<?php declare(strict_types=1);

namespace App\WitAI;

use App\Jenkins\DeployParameters;
use App\Slack\Messages\SlackMentionMessage;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Zend\Diactoros\Request;
use Zend\Diactoros\Uri;

class Adapter
{
    private ClientInterface $httpClient;

    public function __construct(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function recognizeFromSlackMessage(SlackMentionMessage $message): DeployParameters
    {
        $uri = new Uri(sprintf('https://api.wit.ai/message?v=20191006&q=%s', urlencode($message->getText())));
        $authorization = sprintf('Bearer %s', getenv('WITAI_SERVER_TOKEN'));
        $request = (new Request())
            ->withAddedHeader('Authorization', $authorization)
            ->withUri($uri);

        try {
            $response = $this->httpClient->sendRequest($request);
        } catch (ClientExceptionInterface $exception) {
            throw new \LogicException('Shit happened when asking the AI', 0, $exception);
        }

        $responseBody = json_decode((string)$response->getBody(), true);

        return DeployParameters::create($responseBody, $message);
    }
}
