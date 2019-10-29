<?php declare(strict_types=1);

namespace App\Jenkins\Factory;

use App\Jenkins\Service;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;

class ServiceFactory
{
    public function __invoke(ContainerInterface $container): Service
    {
        $token = getenv('JENKINS_JOB_TOKEN');
        $authorization = getenv('JENKINS_AUTHORIZATION');

        return new Service(
            $token,
            $authorization,
            $container->get(ClientInterface::class)
        );
    }
}
