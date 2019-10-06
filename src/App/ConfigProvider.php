<?php

declare(strict_types=1);

namespace App;

use App\Handler\Factory\SlackRequestHandlerFactory;
use App\Handler\SlackRequestHandler;
use App\WitAI\Adapter;
use App\WitAI\Factory\AdapterFactory;

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
            'factories' => [
                SlackRequestHandler::class => SlackRequestHandlerFactory::class,
                Adapter::class => AdapterFactory::class,
            ],
        ];
    }
}
