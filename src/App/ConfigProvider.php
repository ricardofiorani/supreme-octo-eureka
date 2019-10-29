<?php declare(strict_types=1);

namespace App;

use App\Domain\Action\Builder\ActionBuilder;
use App\Domain\Action\Builder\Factory\ActionBuilderFactory;
use App\Http\Request\Handler\Factory\SlackRequestHandlerFactory;
use App\Http\Request\Handler\SlackRequestHandler;
use App\Http\Client\Factory\HttpClientFactory;
use App\Jenkins\Factory\ServiceFactory as JenkinsServiceFactory;
use App\Jenkins\Service as JenkinsService;
use App\Logger\Factory\LoggerFactory;
use App\Service\ActionService;
use App\Service\Factory\ActionServiceFactory;
use App\Slack\Factory\MessengerFactory;
use App\Slack\Messenger;
use App\Slack\Permission\PermissionChecker;
use App\WitAI\Adapter;
use App\WitAI\Factory\AdapterFactory;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;

/**
 * The configuration provider for the App module
 *
 * @see https://docs.zendframework.com/zend-component-installer/
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     *
     */
    public function __invoke() : array
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies() : array
    {
        return [
            'invokables' => [
                PermissionChecker::class => PermissionChecker::class,
            ],
            'factories' => [
                JenkinsService::class => JenkinsServiceFactory::class,
                ActionService::class => ActionServiceFactory::class,
                SlackRequestHandler::class => SlackRequestHandlerFactory::class,
                Adapter::class => AdapterFactory::class,
                ClientInterface::class => HttpClientFactory::class,
                Messenger::class => MessengerFactory::class,
                LoggerInterface::class => LoggerFactory::class,
                ActionBuilder::class => ActionBuilderFactory::class,
            ],
        ];
    }
}
