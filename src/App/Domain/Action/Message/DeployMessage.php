<?php declare(strict_types=1);

namespace App\Domain\Action\Message;

use App\Domain\Intent\DeployIntent;

class DeployMessage
{
    private DeployIntent $intent;

    public function __construct(DeployIntent $intent)
    {
        $this->intent = $intent;
    }

    public function __toString(): string
    {
        $intentName = $this->intent->getType();
        $build = $this->intent->getBuildNumber();
        $environment = $this->intent->getEnvironment();
        $market = $this->intent->getMarket();

        return <<<STRING
I got it ! I will `{$intentName}` build `#{$build}` to `{$environment}` with the `{$market}` market :thumbsup:
STRING;
    }
}
