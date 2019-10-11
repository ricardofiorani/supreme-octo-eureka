<?php declare(strict_types=1);

namespace App\Domain\Action\Exception\Environment;

use App\Domain\Action\Exception\AbstractActionException;

class MultipleEnvironmentsException extends AbstractActionException
{
    public function getFriendlyMessage(): string
    {
        $intent = $this->getFirstIntent();
        $environmentList = $this->getEntitiesCollection()->getEnvironmentEntities();
        $environments = implode('`, `', $environmentList);

        return <<<STRING
I want to `{$intent}` it for you but I found these multiple environments : `{$environments}`. 
I can only deploy to one environment per request.
STRING;
    }
}
