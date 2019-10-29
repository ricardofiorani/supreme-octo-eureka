<?php declare(strict_types=1);

namespace App\WitAI;

use App\WitAI\Domain\Response;
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

    public function recognizeEntities(string $message): Response
    {
        $uri = new Uri(sprintf('https://api.wit.ai/message?v=20191006&q=%s', urlencode($message)));
        $authorization = sprintf('Bearer %s', getenv('WITAI_SERVER_TOKEN'));
        $request = (new Request())
            ->withAddedHeader('Authorization', $authorization)
            ->withUri($uri);

        try {
            $response = $this->httpClient->sendRequest($request);

            return Response::createFromPsrResponse($response);
        } catch (ClientExceptionInterface $exception) {
            throw new AdapterException(
                "There was some problem with Request or Response from Wit.AI: {$exception->getMessage()}",
                0,
                $exception
            );
        }
    }
}
