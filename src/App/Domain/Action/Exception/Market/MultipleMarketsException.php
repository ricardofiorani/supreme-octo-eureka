<?php declare(strict_types=1);

namespace App\Domain\Action\Exception\Market;

use App\Domain\Action\Exception\AbstractActionException;

class MultipleMarketsException extends AbstractActionException
{
    public function getFriendlyMessage(): string
    {
        $intentList = $this->getEntitiesCollection()->getIntentEntities();
        $intents = implode('`, `', reset($intentList));

        return <<<STRING
Sorry, I detected these intents `{$intents}` but I'm not multitask (yet :wink:). 
I can `build` a branch, `deploy` a build number or `build and deploy` a branch.
STRING;
    }
}
