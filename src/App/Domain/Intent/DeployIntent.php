<?php declare(strict_types=1);

namespace App\Domain\Intent;

use App\Domain\Intent\Traits\DeployParametersTrait;
use App\Domain\Parameter\ParameterTypes;
use App\WitAI\Domain\Entity;

class DeployIntent implements IntentInterface
{
    use DeployParametersTrait;

    private Entity $buildNumber;

    public function getType(): string
    {
        return Types::DEPLOY;
    }

    public function getBuildNumber(): Entity
    {
        return $this->buildNumber;
    }

    public function setBuildNumber(Entity $buildNumber): void
    {
        $this->buildNumber = $buildNumber;
    }

    public function getParametersUsed(): array
    {
        return [
            ParameterTypes::INTENT => $this->getType(),
            ParameterTypes::BUILD_NUMBER => $this->getBuildNumber(),
            ParameterTypes::ENVIRONMENT => $this->getEnvironment(),
            ParameterTypes::MARKET => $this->getMarket(),
        ];
    }
}
