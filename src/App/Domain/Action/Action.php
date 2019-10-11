<?php declare(strict_types=1);

namespace App\Domain\Action;

use App\Domain\Intent\IntentInterface;

class Action
{
    private IntentInterface $intent;

    public function __construct(IntentInterface $intent)
    {
        $this->intent = $intent;
    }

    public function getParametersUsed(): array
    {
        return $this->intent->getParametersUsed();
    }

    public function getIntent(): IntentInterface
    {
        return $this->intent;
    }

    public function getIntentType(): string
    {
        return $this->intent->getType();
    }

}
