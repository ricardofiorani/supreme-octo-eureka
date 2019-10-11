<?php declare(strict_types=1);

namespace App\Domain\Action\Exception\BuildNumber;

use App\Domain\Action\Exception\AbstractActionException;

class NoBuildNumberException extends AbstractActionException
{
    public function getFriendlyMessage(): string
    {
        $intent = $this->getFirstIntent();

        return <<<STRING
I want to `{$intent}` it for you, but you need to specify a build number.
It should be something like `#123` or `456`.
STRING;
    }
}
