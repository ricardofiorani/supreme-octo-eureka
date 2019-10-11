<?php declare(strict_types=1);

namespace App\Domain\Intent\Traits;

use App\WitAI\Domain\Entity;

trait BranchOnIntentTrait
{
    private Entity $branch;

    public function setBranch(Entity $branch): void
    {
        $this->branch = $branch;
    }

    public function getBranch(): Entity
    {
        return $this->branch;
    }
}
