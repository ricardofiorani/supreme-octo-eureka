<?php declare(strict_types=1);

namespace App\Domain\Action\Message\Builder;

use App\Domain\Action\Action;
use App\Domain\Action\Message\BuildAndDeployMessage;
use App\Domain\Action\Message\BuildMessage;
use App\Domain\Action\Message\DeployMessage;
use App\Domain\Intent\BuildAndDeployIntent;
use App\Domain\Intent\BuildIntent;
use App\Domain\Intent\DeployIntent;
use App\Domain\Intent\Types;

class MessageBuilder
{
    public static function createFromAction(Action $action): string
    {
        $intent = $action->getIntent();

        switch ($action->getIntentType()) {
            case Types::DEPLOY:
                /** @var DeployIntent $intent */
                return (string)new DeployMessage($intent);
            case Types::BUILD:
                /** @var BuildIntent $intent */
                return (string)new BuildMessage($intent);
            case Types::BUILD_AND_DEPLOY:
                /** @var BuildAndDeployIntent $intent */
                return (string)new BuildAndDeployMessage($intent);
            default:
                throw new \LogicException("The intent {$intent} is not recognized...");
        }
    }
}
