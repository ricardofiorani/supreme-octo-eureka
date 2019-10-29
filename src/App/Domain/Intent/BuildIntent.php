<?php declare(strict_types=1);

namespace App\Domain\Intent;

use App\Domain\Intent\Traits\BranchOnIntentTrait;
use App\Domain\Intent\Traits\IntentEntityTrait;
use App\Domain\Parameter\ParameterTypes;

class BuildIntent implements IntentInterface
{
    use IntentEntityTrait;
    use BranchOnIntentTrait;

    public function getType(): string
    {
        return Types::BUILD;
    }

    public function getParametersUsed(): array
    {
        return [
            ParameterTypes::INTENT => $this->getType(),
            ParameterTypes::BRANCH => $this->getBranch(),
        ];
    }
}
