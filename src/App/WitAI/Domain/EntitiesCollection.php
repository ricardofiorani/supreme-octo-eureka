<?php declare(strict_types=1);

namespace App\WitAI\Domain;

class EntitiesCollection
{
    private array $intentEntities;
    private array $branchEntities;
    private array $environmentEntities;
    private array $buildNumberEntities;
    private array $marketEntities;

    public function __construct(
        array $intentEntities,
        array $branchEntities,
        array $environmentEntities,
        array $buildNumberEntities,
        array $marketEntities
    ) {
        $this->intentEntities = $intentEntities;
        $this->branchEntities = $branchEntities;
        $this->environmentEntities = $environmentEntities;
        $this->buildNumberEntities = $buildNumberEntities;
        $this->marketEntities = $marketEntities;
    }

    public function getIntentEntities(): array
    {
        return $this->intentEntities;
    }

    public function getBranchEntities(): array
    {
        return $this->branchEntities;
    }

    public function getEnvironmentEntities(): array
    {
        return $this->environmentEntities;
    }

    public function getBuildNumberEntities(): array
    {
        return $this->buildNumberEntities;
    }

    public function getMarketEntities(): array
    {
        return $this->marketEntities;
    }

    public function toArray(): array
    {
        return [
            'intentEntities' => $this->extractEntities($this->intentEntities),
            'branchEntities' => $this->extractEntities($this->branchEntities),
            'environmentEntities' => $this->extractEntities($this->environmentEntities),
            'buildNumberEntities' => $this->extractEntities($this->buildNumberEntities),
            'marketEntities' => $this->extractEntities($this->marketEntities),
        ];
    }

    private function extractEntities(array $entities): array
    {
        $output = [];

        /** @var Entity $entity */
        foreach ($entities as $entity) {
            $output[] = $entity->toArray();
        }

        return $output;
    }
}
