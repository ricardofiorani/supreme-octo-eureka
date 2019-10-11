<?php declare(strict_types=1);

namespace App\Domain\Action\Exception\Branch;

use App\Domain\Action\Exception\AbstractActionException;

class MultipleBranchesException extends AbstractActionException
{
    public function getFriendlyMessage(): string
    {
        $branchList = $this->getEntitiesCollection()->getBranchEntities();
        $branches = implode('`, `', $branchList);

        return <<<STRING
I found these branches `{$branches}` but I can only build one at a time, please ask again with one branch only.
STRING;
    }
}
