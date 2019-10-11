<?php declare(strict_types=1);

namespace App\Domain\Action\Exception\BuildNumber;

use App\Domain\Action\Exception\AbstractActionException;

class MultipleBuildNumbersException extends AbstractActionException
{
    public function getFriendlyMessage(): string
    {
        $buildList = $this->getEntitiesCollection()->getBuildNumberEntities();
        $buildNumbers = implode('`, `', $buildList);

        return <<<STRING
I want to deploy it for you but I found these multiple values : `{$buildNumbers}`. 
I can only deploy one build number per request.
STRING;
    }
}
