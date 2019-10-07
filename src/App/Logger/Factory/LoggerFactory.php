<?php declare(strict_types=1);

namespace App\Logger\Factory;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class LoggerFactory
{
    public function __invoke(ContainerInterface $container): LoggerInterface
    {
        $logger = new Logger('log');
        $logger->pushHandler(new StreamHandler('php://stdout'));

        return $logger;
    }
}
