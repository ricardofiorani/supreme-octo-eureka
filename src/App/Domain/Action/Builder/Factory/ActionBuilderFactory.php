<?php declare(strict_types=1);

namespace App\Domain\Action\Builder\Factory;

use App\Domain\Action\Builder\ActionBuilder;
use App\Slack\Permission\PermissionChecker;
use Psr\Container\ContainerInterface;

class ActionBuilderFactory
{
    public function __invoke(ContainerInterface $container): ActionBuilder
    {
        return new ActionBuilder(
            $container->get(PermissionChecker::class)
        );
    }
}
