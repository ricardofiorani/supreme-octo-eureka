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
        $intentConfidence = $this->intent->getEntity()->getConfidencePercentage();
        $branchName = $this->intent->getBranch();
        $branchConfidence = $this->intent->getBranch()->getConfidencePercentage();
        $environmentName = $this->intent->getEnvironment();
        $environmentConfidence = $this->intent->getEntity()->getConfidencePercentage();
        $marketName = $this->intent->getMarket();
        $marketConfidence = $this->intent->getMarket()->getConfidencePercentage();

        return <<<STRING
I got it :thumbs_up:, I will `{$intentName}` the branch `{$branchName}` to `{$environmentName}` with the `{$marketName}` market ! :shipitparrot:
I got it :thumbsup:, I will `{$intentName}` ({$intentConfidence} confidence) the branch `{$branchName}` ({$branchConfidence} confidence) to `{$environmentName}` ({$environmentConfidence} confidence) with the `{$marketName}` ({$marketConfidence} confidence) market ! :shipitparrot:
STRING;
    }
}
