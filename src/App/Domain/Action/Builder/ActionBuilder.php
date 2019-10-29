<?php declare(strict_types=1);

namespace App\Domain\Action\Builder;

use App\Domain\Action\Action;
use App\Domain\Action\ActionParameters;
use App\Domain\Action\Exception\AbstractActionException;
use App\Domain\Action\Exception\Environment\MultipleEnvironmentsException;
use App\Domain\Action\Exception\Environment\NoEnvironmentException;
use App\Domain\Action\Exception\Intent\InvalidIntentException;
use App\Domain\Action\Exception\Branch\MultipleBranchesException;
use App\Domain\Action\Exception\BuildNumber\MultipleBuildNumbersException;
use App\Domain\Action\Exception\Intent\MultipleIntentsException;
use App\Domain\Action\Exception\Branch\NoBranchParameterException;
use App\Domain\Action\Exception\BuildNumber\NoBuildNumberException;
use App\Domain\Action\Exception\Intent\NoIntentException;
use App\Domain\Action\Exception\Market\MultipleMarketsException;
use App\Domain\Intent\BuildAndDeployIntent;
use App\Domain\Intent\BuildIntent;
use App\Domain\Intent\DeployIntent;
use App\Domain\Intent\Factory\IntentFactory;
use App\Domain\Intent\Factory\InvalidIntentException as InvalidIntentCreationException;
use App\Domain\Intent\IntentInterface;
use App\Slack\Permission\PermissionChecker;
use App\WitAI\Domain\EntitiesCollection;
use App\WitAI\Domain\Entity;

class ActionBuilder
{
    private PermissionChecker $permissionChecker;

    public function __construct(PermissionChecker $permissionChecker)
    {
        $this->permissionChecker = $permissionChecker;
    }

    /**
     * @throws AbstractActionException
     */
    public function create(ActionParameters $parameters): Action
    {
        $entitiesCollection = $parameters->getEntities();
        $user = $parameters->getUser();

        $this->validateIntents($entitiesCollection);
        $intent = $this->extractIntent($entitiesCollection);

        switch (get_class($intent)) {
            case BuildIntent::class:
                /** @var BuildIntent $intent */
                $this->validateBranch($entitiesCollection);
                $branch = $this->extractBranch($entitiesCollection);
                $intent->setBranch($branch);

                break;
            case BuildAndDeployIntent::class:
                /** @var BuildAndDeployIntent $intent */
                $this->validateBranch($entitiesCollection);
                $branch = $this->extractBranch($entitiesCollection);
                $intent->setBranch($branch);

                $this->validateEnvironment($entitiesCollection, $parameters->getUser());
                $environment = $this->extractEnvironment($entitiesCollection);
                $intent->setEnvironment($environment);

                $this->validateMarket($entitiesCollection);
                $market = $this->extractMarket($entitiesCollection);
                $intent->setMarket($market);

                break;
            case DeployIntent::class:
                /** @var DeployIntent $intent */
                $this->validateBuildNumber($entitiesCollection);
                $buildNumber = $this->extractBuildNumber($entitiesCollection);
                $intent->setBuildNumber($buildNumber);

                $this->validateEnvironment($entitiesCollection, $parameters->getUser());
                $environment = $this->extractEnvironment($entitiesCollection);
                $intent->setEnvironment($environment);

                $this->validateMarket($entitiesCollection);
                $market = $this->extractMarket($entitiesCollection);
                $intent->setMarket($market);
                break;
        }

        return new Action($intent, $user);
    }

    /**
     * @throws AbstractActionException
     */
    private function extractIntent(EntitiesCollection $entitiesCollection): IntentInterface
    {
        /** @var Entity $intentEntity */
        try {
            $intents = $entitiesCollection->getIntentEntities();
            return IntentFactory::createFromEntity(reset($intents));
        } catch (InvalidIntentCreationException $e) {
            throw new InvalidIntentException($entitiesCollection);
        }
    }

    /**
     * @throws AbstractActionException
     */
    private function validateIntents(EntitiesCollection $entitiesCollection): void
    {
        $intents = $entitiesCollection->getIntentEntities();

        if (empty($intents)) {
            throw new NoIntentException($entitiesCollection);
        }

        if (count($intents) > 1) {
            throw new MultipleIntentsException($entitiesCollection);
        }
    }

    /**
     * @throws AbstractActionException
     */
    private function validateBranch(EntitiesCollection $entitiesCollection): void
    {
        $branches = $entitiesCollection->getBranchEntities();

        if (empty($branches)) {
            throw new NoBranchParameterException($entitiesCollection);
        }

        if (count($branches) > 1) {
            throw new MultipleBranchesException($entitiesCollection);
        }
    }

    private function extractBranch(EntitiesCollection $entitiesCollection): Entity
    {
        $branchEntities = $entitiesCollection->getBranchEntities();

        return reset($branchEntities);
    }

    /**
     * @throws AbstractActionException
     */
    private function validateBuildNumber(EntitiesCollection $entitiesCollection): void
    {
        $buildNumberEntities = $entitiesCollection->getBuildNumberEntities();

        if (empty($buildNumberEntities)) {
            throw new NoBuildNumberException($entitiesCollection);
        }

        if (count($buildNumberEntities) > 1) {
            throw new MultipleBuildNumbersException($entitiesCollection);
        }
    }

    private function extractBuildNumber(EntitiesCollection $entitiesCollection): Entity
    {
        $buildNumberEntities = $entitiesCollection->getBuildNumberEntities();

        return reset($buildNumberEntities);
    }

    /**
     * @throws AbstractActionException
     */
    private function validateEnvironment(EntitiesCollection $entitiesCollection, string $user): void
    {
        $environmentEntities = $entitiesCollection->getEnvironmentEntities();

        if (empty($environmentEntities)) {
            throw new NoEnvironmentException($entitiesCollection);
        }

        if (count($environmentEntities) > 1) {
            throw new MultipleEnvironmentsException($entitiesCollection);
        }

        /** @var Entity $environment */
        $environment = reset($environmentEntities);

        if ($environment->getValue() === 'production') {
            $this->permissionChecker->checkPermissionToDeployToProduction($user);
        }
    }

    private function extractEnvironment(EntitiesCollection $entitiesCollection): Entity
    {
        $environmentEntities = $entitiesCollection->getEnvironmentEntities();

        return reset($environmentEntities);
    }

    /**
     * @throws AbstractActionException
     */
    private function validateMarket(EntitiesCollection $entitiesCollection): void
    {
        $marketEntities = $entitiesCollection->getMarketEntities();

        if (count($marketEntities) > 1) {
            throw new MultipleMarketsException($entitiesCollection);
        }
    }

    private function extractMarket(EntitiesCollection $entitiesCollection): Entity
    {
        $marketEntities = $entitiesCollection->getMarketEntities();
        $entity = reset($marketEntities);

        if ($entity instanceof Entity) {
            return $entity;
        }

        return new Entity(true, 0.01, 'urlaubspiratende', 'value');
    }
}
