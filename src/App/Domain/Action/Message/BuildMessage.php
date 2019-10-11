<?php declare(strict_types=1);

namespace App\Domain\Action\Message;

use App\Domain\Intent\BuildIntent;

class BuildMessage
{
    private BuildIntent $intent;

    public function __construct(BuildIntent $intent)
    {
        $this->intent = $intent;
    }

    public function __toString(): string
    {
        $intentName = $this->intent->getType();
        $branch = $this->intent->getBranch();

        return <<<STRING
I got it ! I will `{$intentName}` the branch `{$branch}` :thumbsup:
STRING;
    }
}
