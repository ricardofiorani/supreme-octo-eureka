<?php declare(strict_types=1);

namespace App\Domain\Action\Exception\Intent;

use App\Domain\Action\Exception\AbstractActionException;

class NoIntentException extends AbstractActionException
{
    public function getFriendlyMessage(): string
    {
        return <<<STRING
Sorry, what you want me to do ? I can :
- `build` a branch
- `deploy` a build number to a certain environment (even emulating a market if it's a beta or staging environment).
- `build and deploy` a branch to a certain environment.
STRING;
    }
}
