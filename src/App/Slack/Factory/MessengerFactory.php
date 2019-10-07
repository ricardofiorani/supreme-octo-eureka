<?php declare(strict_types=1);

namespace App\Slack\Factory;

use App\Slack\Messenger;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;

class MessengerFactory
{
    public function __invoke(ContainerInterface $container): Messenger
    {
        return new Messenger(
            $container->get(ClientInterface::class)
        );
    }
}
