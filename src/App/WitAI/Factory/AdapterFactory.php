<?php declare(strict_types=1);

namespace App\WitAI\Factory;

use App\WitAI\Adapter;
use Psr\Container\ContainerInterface;
use RicardoFiorani\GuzzlePsr18Adapter\Client as HttpClient;

class AdapterFactory
{
    public function __invoke(ContainerInterface $container): Adapter
    {
        return new Adapter(
            new HttpClient() //We could get from the container but meh... this is just a proof-of-concept
        );
    }
}
