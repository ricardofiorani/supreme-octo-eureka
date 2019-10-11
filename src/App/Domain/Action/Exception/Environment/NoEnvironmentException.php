<?php declare(strict_types=1);

namespace App\Domain\Action\Exception\Environment;

use App\Domain\Action\Exception\AbstractActionException;

class NoEnvironmentException extends AbstractActionException
{
    public function getFriendlyMessage(): string
    {
        $intent = $this->getFirstIntent();

        return <<<STRING
I want to `{$intent}` for you but you must provide a valid environment !
You can try something like `urlaubspiratende`, `de` or `german market` that I understand (I hope :sweat_smile:)
STRING;
    }
}
