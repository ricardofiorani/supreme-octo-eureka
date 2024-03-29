<?php declare(strict_types=1);

namespace App\WitAI\Factory;

use App\WitAI\Adapter;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;

class AdapterFactory
{
    public function __invoke(ContainerInterface $container): Adapter
    {
        return new Adapter(
            $container->get(ClientInterface::class)
        );
    }
}
