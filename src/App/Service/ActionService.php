<?php declare(strict_types=1);

namespace App\Service;

use App\Domain\Action\Builder\ActionBuilder;
use App\Domain\Action\Exception\AbstractActionException;
use App\Domain\Action\Message\Builder\MessageBuilder;
use App\Domain\Action\Response\ActionResponse;
use App\WitAI\Domain\Response;

class ActionService
{
    private ActionBuilder $actionBuilder;

    public function processEntities(Response $response): ActionResponse
    {
        $this->actionBuilder = new ActionBuilder($response->getEntities());

        try {
            $action = $this->actionBuilder->create();
        } catch (AbstractActionException $exception) {
            return new ActionResponse(false, $exception->getFriendlyMessage(), []);
        }

        return new ActionResponse(
            true,
            MessageBuilder::createFromAction($action),
            $action->getParametersUsed()
        );
    }
}
