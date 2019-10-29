<?php declare(strict_types=1);

namespace App\Domain\Action;

use App\Domain\Intent\IntentInterface;

class Action
{
    private IntentInterface $intent;
    private string $user;

    public function __construct(IntentInterface $intent, string $user)
    {
        $this->intent = $intent;
        $this->user = $user;
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

    public function getUser(): string
    {
        return $this->user;
    }
}
