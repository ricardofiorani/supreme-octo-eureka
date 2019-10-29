<?php declare(strict_types=1);

namespace App\Domain\Action\Response;

class ActionResponse
{
    private bool $isSuccessful;
    private string $message;
    private array $parametersUsed;

    public function __construct(bool $isSuccessful, string $message, array $parametersUsed)
    {
        $this->isSuccessful = $isSuccessful;
        $this->message = $message;
        $this->parametersUsed = $parametersUsed;
    }

    public function getResponseMessage(): string
    {
        return $this->message;
    }

    public function isSuccessful(): bool
    {
        return $this->isSuccessful;
    }

    public function getParametersUsed(): array
    {
        return $this->parametersUsed;
    }
}
