<?php declare(strict_types=1);

namespace App\Domain\Action\Exception\Branch;

use App\Domain\Action\Exception\AbstractActionException;

class NoBranchParameterException extends AbstractActionException
{
    public function getFriendlyMessage(): string
    {
        $intent = $this->getFirstIntent();

        return <<<STRING
I want to `{$intent}` for you but you must provide a valid branch !
It can be something like a git commit id but preferably a branch name.
Product manager pro tip: You can check on Jira which is the branch name linked with the ticket.
STRING;
    }
}
