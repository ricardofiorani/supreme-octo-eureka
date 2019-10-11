<?php declare(strict_types=1);

namespace App\Domain\Action\Exception\Intent;

use App\Domain\Action\Exception\AbstractActionException;

class InvalidIntentException extends AbstractActionException
{
    public function getFriendlyMessage(): string
    {
        $intentEntity = $this->getEntitiesCollection()->getIntentEntities();
        $intent = reset($intentEntity);

        return <<<STRING
I don't know how to perform a `{$intent}`... I'm sorry :(
STRING;
    }
}
