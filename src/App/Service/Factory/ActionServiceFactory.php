<?php declare(strict_types=1);

namespace App\Service\Factory;

use App\Domain\Action\Builder\ActionBuilder;
use App\Jenkins\Service as JenkinsService;
use App\Service\ActionService;
use Psr\Container\ContainerInterface;

class ActionServiceFactory
{
    public function __invoke(ContainerInterface $container): ActionService
    {
        return new ActionService(
            $container->get(JenkinsService::class),
            $container->get(ActionBuilder::class),
        );
    }
}
