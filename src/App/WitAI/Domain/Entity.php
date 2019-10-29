<?php declare(strict_types=1);

namespace App\WitAI\Domain;

class Entity
{
    private bool $suggested;
    private float $confidence;
    private string $value;
    private string $type;

    public function __construct(bool $suggested, float $confidence, string $value, string $type)
    {
        $this->suggested = $suggested;
        $this->confidence = $confidence;
        $this->value = $value;
        $this->type = $type;
    }

    public static function createFromArray(array $entity): self
    {
        return new self(
            $entity['suggested'] ?? false,
            $entity['confidence'],
            $entity['value'],
            $entity['type'],
        );
    }

    public function isSuggested(): bool
    {
        return $this->suggested;
    }

    public function getConfidence(): float
    {
        return $this->confidence;
    }

    public function getConfidencePercentage(): string
    {
        return round($this->getConfidence() * 100) . '%';
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function toArray(): array
    {
        return [
            'suggested' => $this->suggested,
            'confidence' => $this->confidence,
            'value' => $this->value,
            'type' => $this->type,
        ];
    }
}
