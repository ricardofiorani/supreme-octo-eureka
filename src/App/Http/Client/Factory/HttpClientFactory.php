<?php declare(strict_types=1);

namespace App\Http\Client\Factory;

use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;
use RicardoFiorani\GuzzlePsr18Adapter\Client;

class HttpClientFactory
{
    public function __invoke(ContainerInterface $container): ClientInterface
    {
        return new Client();
    }
}
