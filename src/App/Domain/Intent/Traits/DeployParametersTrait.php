<?php declare(strict_types=1);

namespace App\Domain\Intent\Traits;

use App\WitAI\Domain\Entity;

trait DeployParametersTrait
{
    private Entity $market;
    private Entity $environment;

    public function getMarket(): Entity
    {
        return $this->market;
    }

    public function getEnvironment(): Entity
    {
        return $this->environment;
    }

    public function setMarket(Entity $market): void
    {
        $this->market = $market;
    }

    public function setEnvironment(Entity $environment): void
    {
        $this->environment = $environment;
    }
}
