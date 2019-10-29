<?php declare(strict_types=1);

namespace App\Domain\Intent;

use App\Domain\Intent\Traits\BranchOnIntentTrait;
use App\Domain\Intent\Traits\DeployParametersTrait;
use App\Domain\Intent\Traits\IntentEntityTrait;
use App\Domain\Parameter\ParameterTypes;

class BuildAndDeployIntent implements IntentInterface
{
    use IntentEntityTrait;
    use BranchOnIntentTrait;
    use DeployParametersTrait;

    public function getType(): string
    {
        return Types::BUILD_AND_DEPLOY;
    }

    public function getParametersUsed(): array
    {
        return [
            ParameterTypes::INTENT => $this->getType(),
            ParameterTypes::BRANCH => $this->getBranch(),
            ParameterTypes::ENVIRONMENT => $this->getEnvironment(),
            ParameterTypes::MARKET => $this->getMarket(),
        ];
    }
}
