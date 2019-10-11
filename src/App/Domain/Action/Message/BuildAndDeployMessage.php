<?php declare(strict_types=1);

namespace App\Domain\Action\Message;

use App\Domain\Intent\BuildAndDeployIntent;

class BuildAndDeployMessage
{
    private BuildAndDeployIntent $intent;

    public function __construct(BuildAndDeployIntent $intent)
    {
        $this->intent = $intent;
    }

    public function __toString(): string
    {
        $intentName = $this->intent->getType();
        $branch = $this->intent->getBranch();
        $environment = $this->intent->getEnvironment();
        $market = $this->intent->getMarket();

        return <<<STRING
I got it ! I will `{$intentName}` the branch `{$branch}` to `{$environment}` with the `{$market}` market :thumbsup:
STRING;
    }
}
