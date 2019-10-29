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
        $buildNumber = $this->intent->getBuildNumber();
        $buildNumberConfidence = $this->intent->getBuildNumber()->getConfidencePercentage();

        $intentName = $this->intent->getType();
        $intentConfidence = $this->intent->getEntity()->getConfidencePercentage();

        $environmentName = $this->intent->getEnvironment();
        $environmentConfidence = $this->intent->getEntity()->getConfidencePercentage();

        $marketName = $this->intent->getMarket();
        $marketConfidence = $this->intent->getMarket()->getConfidencePercentage();

        return <<<STRING
I got it :thumbsup:, I will `{$intentName}` ({$intentConfidence} confidence) build `#{$buildNumber}` ({$buildNumberConfidence} confidence) to `{$environmentName}` ({$environmentConfidence} confidence) with the `{$marketName}` ({$marketConfidence} confidence) market ! :shipitparrot:
STRING;
    }
}
