<?php declare(strict_types=1);

namespace App\Http\Request\Handler\Factory;

use App\Http\Request\Handler\SlackRequestHandler;
use App\Service\ActionService;
use App\Slack\Messenger;
use App\Slack\Permission\PermissionChecker;
use App\WitAI\Adapter as AI;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class SlackRequestHandlerFactory
{
    public function __invoke(ContainerInterface $container): SlackRequestHandler
    {
        return new SlackRequestHandler(
            $container->get(AI::class),
            $container->get(Messenger::class),
            $container->get(ActionService::class),
            $container->get(PermissionChecker::class),
            $container->get(LoggerInterface::class)
        );
    }
}
