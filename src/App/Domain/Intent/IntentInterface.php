<?php declare(strict_types=1);

namespace App\Domain\Intent;

interface IntentInterface
{
    public function getType(): string;

    public function getParametersUsed(): array;
}
