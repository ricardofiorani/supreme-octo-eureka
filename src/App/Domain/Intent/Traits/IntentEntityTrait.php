<?php declare(strict_types=1);

namespace App\Domain\Intent\Traits;

use App\WitAI\Domain\Entity;

trait IntentEntityTrait
{
    private Entity $entity;

    public function getEntity(): Entity
    {
        return $this->entity;
    }

    public function setEntity(Entity $entity): void
    {
        $this->entity = $entity;
    }
}
