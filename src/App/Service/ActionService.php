<?php declare(strict_types=1);

namespace App\Service;

use App\Domain\Action\ActionParameters;
use App\Domain\Action\Builder\ActionBuilder;
use App\Domain\Action\Exception\AbstractActionException;
use App\Domain\Action\Message\Builder\MessageBuilder;
use App\Domain\Action\Response\ActionResponse;
use App\Jenkins\Exception\JenkinsException;
use App\Jenkins\Service as JenkinsService;

class ActionService
{
    private ActionBuilder $actionBuilder;
    private JenkinsService $jenkinsService;

    public function __construct(JenkinsService $jenkinsService, ActionBuilder $actionBuilder)
    {
        $this->jenkinsService = $jenkinsService;
        $this->actionBuilder = $actionBuilder;
    }

    public function process(ActionParameters $parameters): ActionResponse
    {
        try {
            $action = $this->actionBuilder->create($parameters);

            return new ActionResponse(
                $this->jenkinsService->processAction($action),
                MessageBuilder::createFromAction($action),
                $action->getParametersUsed()
            );
        } catch (AbstractActionException $exception) {
            return new ActionResponse(false, $exception->getFriendlyMessage(), []);
        } catch (JenkinsException $exception) {
            return new ActionResponse(false, $exception->getMessage(), []);
        }
    }
}
