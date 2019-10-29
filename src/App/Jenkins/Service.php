<?php declare(strict_types=1);

namespace App\Jenkins;

use App\Domain\Action\Action;
use App\Domain\Intent\Types;
use App\Domain\Parameter\ParameterTypes;
use App\Jenkins\Exception\JenkinsException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Zend\Diactoros\Request;
use Zend\Diactoros\Uri;

class Service
{
    private string $jobToken;
    private string $jenkinsAuth;
    private array $endpoints;
    private ClientInterface $httpClient;

    public function __construct(string $jobToken, string $jenkinsAuth, ClientInterface $httpClient)
    {
        $this->jobToken = $jobToken;
        $this->jenkinsAuth = $jenkinsAuth;
        $this->httpClient = $httpClient;
        $this->endpoints = [
            Types::BUILD_AND_DEPLOY => getenv('JENKINS_BUILD_AND_DEPLOY_ENDPOINT'),
            Types::BUILD => getenv('JENKINS_BUILD_ENDPOINT'),
            Types::DEPLOY => [
                'production' => getenv('JENKINS_DEPLOY_PRODUCTION_ENDPOINT'),
                'staging' => getenv('JENKINS_DEPLOY_STAGING_ENDPOINT'),
                'beta1' => getenv('JENKINS_DEPLOY_BETA1_ENDPOINT'),
            ],
        ];
    }

    /**
     * @throws JenkinsException
     */
    public function processAction(Action $action): bool
    {
        switch ($action->getIntentType()) {
            case Types::BUILD_AND_DEPLOY:
                $environment = (string)$action->getParametersUsed()[ParameterTypes::ENVIRONMENT];
                $environment = ucfirst($environment);
                $uri = new Uri($this->endpoints[Types::BUILD_AND_DEPLOY]);
                break;
            case Types::BUILD:
                $environment = (string)$action->getParametersUsed()[ParameterTypes::ENVIRONMENT];
                $uri = new Uri($this->endpoints[Types::BUILD]);
                break;
            case Types::DEPLOY:
                $environment = (string)$action->getParametersUsed()[ParameterTypes::ENVIRONMENT];
                $uri = new Uri($this->endpoints[Types::DEPLOY][$environment]);
                break;
            default:
                throw new JenkinsException("The action {$action->getIntentType()} is not supported !");
        }

        try {
            $uri = $uri->withQuery(http_build_query([
                'token' => $this->getJobToken(),
                'BRANCH' => (string)($action->getParametersUsed()[ParameterTypes::BRANCH] ?? 'master'),
                'ENVIRONMENT' => $environment ?? '',
                'BUILD_NUMBER' => (string)$action->getParametersUsed()[ParameterTypes::BUILD_NUMBER],
                'DEFAULT_MARKET' => (string)$action->getParametersUsed()[ParameterTypes::MARKET],
                'MARKET' => (string)$action->getParametersUsed()[ParameterTypes::MARKET],
                'cause' => "Automatically triggered from Slack by user id {$action->getUser()}",
            ]));

            $request = (new Request($uri))->withHeader('Authorization', $this->getJenkinsAuth());
            $response = $this->httpClient->sendRequest($request);

            $statusCode = $response->getStatusCode();

            if (200 !== $statusCode && 201 !== $statusCode) {
                throw new JenkinsException(
                    "The request was sent but it received a {$response->getStatusCode()} status code"
                );
            }
        } catch (ClientExceptionInterface $exception) {
            throw new JenkinsException(
                "There was a problem sending the request: {$exception->getMessage()}",
                0,
                $exception
            );
        }

        return $response->getStatusCode() === 200;
    }

    private function getJobToken(): string
    {
        return $this->jobToken;
    }

    private function getJenkinsAuth(): string
    {
        return $this->jenkinsAuth;
    }
}
