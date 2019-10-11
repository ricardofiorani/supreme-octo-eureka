<?php declare(strict_types=1);

namespace App\Domain\Intent\Factory;

use App\Domain\Intent\BuildAndDeployIntent;
use App\Domain\Intent\BuildIntent;
use App\Domain\Intent\DeployIntent;
use App\Domain\Intent\IntentInterface;
use App\Domain\Intent\Types;

class IntentFactory
{
    public static function createFromString(string $intent): IntentInterface
    {
        switch ($intent) {
            case Types::BUILD:
                return new BuildIntent();
            case Types::DEPLOY:
                return new DeployIntent();
            case Types::BUILD_AND_DEPLOY:
                return new BuildAndDeployIntent();
            default:
                throw new InvalidIntentException("`$intent` is not a valid intent");
        }
    }
}
