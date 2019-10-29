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
        $intentConfidence = $this->intent->getEntity()->getConfidencePercentage();

        $branchName = $this->intent->getBranch();
        $branchConfidence = $this->intent->getBranch()->getConfidencePercentage();

        return <<<STRING
I got it :thumbsup:, I will `{$intentName}` ({$intentConfidence} confidence) the branch `{$branchName}` ({$branchConfidence} confidence) ! :shipitparrot: 
STRING;
    }
}
