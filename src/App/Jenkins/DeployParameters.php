<?php declare(strict_types=1);

namespace App\Jenkins;

class DeployParameters
{
    private string $market;
    private string $environment;
    private string $branch;

    public function __construct(string $market, string $environment, string $branch)
    {
        $this->market = $market;
        $this->environment = $environment;
        $this->branch = $branch;
    }

    public static function createFromArray(array $array): self
    {
        return new self(
            $array['entities']['market_entity'][0]['value'],
            $array['entities']['environment_entity'][0]['value'],
            $array['entities']['branch_entity'][0]['value'],
        );
    }

    public function getMarket(): string
    {
        return $this->market;
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    public function getBranch(): string
    {
        return $this->branch;
    }
}
