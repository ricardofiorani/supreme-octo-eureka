<?php declare(strict_types=1);

namespace App\Domain\Action\Exception;

use App\WitAI\Domain\EntitiesCollection;
use App\WitAI\Domain\Entity;
use Throwable;

abstract class AbstractActionException extends \Exception
{
    private EntitiesCollection $entitiesCollection;

    public function __construct(
        EntitiesCollection $entitiesCollection,
        $message = '',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->entitiesCollection = $entitiesCollection;
    }

    public function getEntitiesCollection(): EntitiesCollection
    {
        return $this->entitiesCollection;
    }

    abstract public function getFriendlyMessage(): string;

    public function getFirstIntent(): Entity
    {
        $intentList = $this->getEntitiesCollection()->getIntentEntities();

        return reset($intentList);
    }
}
