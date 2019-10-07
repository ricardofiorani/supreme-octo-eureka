<?php declare(strict_types=1);

namespace App\Handler\Factory;

use App\Handler\SlackRequestHandler;
use App\Slack\Messenger;
use App\WitAI\Adapter as AI;
use Psr\Container\ContainerInterface;

class SlackRequestHandlerFactory
{
    public function __invoke(ContainerInterface $container): SlackRequestHandler
    {
        return new SlackRequestHandler(
            $container->get(AI::class),
            $container->get(Messenger::class)
        );
    }
}
