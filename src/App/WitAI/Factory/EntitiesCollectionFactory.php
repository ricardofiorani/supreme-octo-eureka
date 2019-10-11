<?php declare(strict_types=1);

namespace App\WitAI\Factory;

use App\WitAI\Domain\EntitiesCollection;
use App\WitAI\Domain\Entity;

class EntitiesCollectionFactory
{
    private const INTENT_ENTITIES_KEY = 'intent_entity';
    private const BRANCH_ENTITIES_KEY = 'branch_entity';
    private const ENVIRONMENT_ENTITIES_KEY = 'environment_entity';
    private const BUILD_NUMBER_ENTITIES_KEY = 'build_number_entity';
    private const MARKET_ENTITIES_KEY = 'market_entity';

    public static function createFromArray(array $input): EntitiesCollection
    {
        $intentEntities = [];
        $branchEntities = [];
        $environmentEntities = [];
        $buildNumberEntities = [];
        $marketEntities = [];

        foreach ($input as $entityType => $entities) {
            switch ($entityType) {
                case self::INTENT_ENTITIES_KEY:
                    $intentEntities = self::createEntitiesFromArray($entities);
                    break;
                case self::BRANCH_ENTITIES_KEY:
                    $branchEntities = self::createEntitiesFromArray($entities);
                    break;
                case self::ENVIRONMENT_ENTITIES_KEY:
                    $environmentEntities = self::createEntitiesFromArray($entities);
                    break;
                case self::BUILD_NUMBER_ENTITIES_KEY:
                    $buildNumberEntities = self::createEntitiesFromArray($entities);
                    break;
                case self::MARKET_ENTITIES_KEY:
                    $marketEntities = self::createEntitiesFromArray($entities);
                    break;
            }
        }

        return new EntitiesCollection(
            $intentEntities,
            $branchEntities,
            $environmentEntities,
            $buildNumberEntities,
            $marketEntities
        );
    }

    private static function createEntitiesFromArray(array $entities): array
    {
        $output = [];

        foreach ($entities as $entity) {
            $output[] = Entity::createFromArray($entity);
        }

        return $output;
    }
}
