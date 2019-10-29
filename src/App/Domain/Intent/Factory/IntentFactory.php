<?php declare(strict_types=1);

namespace App\Domain\Intent\Factory;

use App\Domain\Intent\BuildAndDeployIntent;
use App\Domain\Intent\BuildIntent;
use App\Domain\Intent\DeployIntent;
use App\Domain\Intent\IntentInterface;
use App\Domain\Intent\Types;
use App\WitAI\Domain\Entity;

class IntentFactory
{
    public static function createFromEntity(Entity $entity): IntentInterface
    {
        $intentValue = $entity->getValue();

        switch ($intentValue) {
            case Types::BUILD:
                $intent = new BuildIntent();
                break;
            case Types::DEPLOY:
                $intent = new DeployIntent();
                break;
            case Types::BUILD_AND_DEPLOY:
                $intent = new BuildAndDeployIntent();
                break;
            default:
                throw new InvalidIntentException("`$intentValue` is not a valid intent");
        }

        $intent->setEntity($entity);

        return $intent;
    }
}
