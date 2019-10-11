<?php declare(strict_types=1);

namespace App\Domain\Action\Builder;

use App\Domain\Action\Action;
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
use App\WitAI\Domain\EntitiesCollection;
use App\WitAI\Domain\Entity;

class ActionBuilder
{
    private EntitiesCollection $entitiesCollection;
    private IntentInterface $intent;

    public function __construct(EntitiesCollection $entitiesCollection)
    {
        $this->entitiesCollection = $entitiesCollection;
    }

    public function setIntent(IntentInterface $intent): void
    {
        $this->intent = $intent;
    }

    /**
     * @throws AbstractActionException
     */
    public function create(): Action
    {
        $entities = $this->entitiesCollection;

        $this->validateIntents($entities->getIntentEntities());
        $intent = $this->extractIntent($entities->getIntentEntities());
        $this->setIntent($intent);

        switch (get_class($intent)) {
            case BuildIntent::class:
                /** @var BuildIntent $intent */
                $this->validateBranch($entities->getBranchEntities());
                $branch = $this->extractBranch($entities->getBranchEntities());
                $intent->setBranch($branch);

                break;
            case BuildAndDeployIntent::class:
                /** @var BuildAndDeployIntent $intent */
                $this->validateBranch($entities->getBranchEntities());
                $branch = $this->extractBranch($entities->getBranchEntities());
                $intent->setBranch($branch);

                $this->validateEnvironment($entities->getEnvironmentEntities());
                $environment = $this->extractEnvironment($entities->getEnvironmentEntities());
                $intent->setEnvironment($environment);

                $this->validateMarket($entities->getMarketEntities());
                $market = $this->extractMarket($entities->getMarketEntities());
                $intent->setMarket($market);

                break;
            case DeployIntent::class:
                /** @var DeployIntent $intent */
                $this->validateBuildNumber($entities->getBuildNumberEntities());
                $buildNumber = $this->extractBuildNumber($entities->getBuildNumberEntities());
                $intent->setBuildNumber($buildNumber);

                $this->validateEnvironment($entities->getEnvironmentEntities());
                $environment = $this->extractEnvironment($entities->getEnvironmentEntities());
                $intent->setEnvironment($environment);

                $this->validateMarket($entities->getMarketEntities());
                $market = $this->extractMarket($entities->getMarketEntities());
                $intent->setMarket($market);
                break;
        }

        return new Action($intent);
    }

    /**
     * @throws AbstractActionException
     */
    private function extractIntent(array $intents): IntentInterface
    {
        /** @var Entity $intentEntity */
        try {
            return IntentFactory::createFromString(reset($intents)->getValue());
        } catch (InvalidIntentCreationException $e) {
            throw new InvalidIntentException($this->entitiesCollection);
        }
    }

    /**
     * @throws AbstractActionException
     */
    private function validateIntents(array $intents): void
    {
        if (empty($intents)) {
            throw new NoIntentException($this->entitiesCollection);
        }

        if (count($intents) > 1) {
            throw new MultipleIntentsException($this->entitiesCollection);
        }
    }

    /**
     * @throws AbstractActionException
     */
    private function validateBranch(array $branches): void
    {
        if (empty($branches)) {
            throw new NoBranchParameterException($this->entitiesCollection);
        }

        if (count($branches) > 1) {
            throw new MultipleBranchesException($this->entitiesCollection);
        }
    }

    private function extractBranch(array $branchEntities): Entity
    {
        return reset($branchEntities);
    }

    /**
     * @throws AbstractActionException
     */
    private function validateBuildNumber(array $buildNumberEntities): void
    {
        if (empty($buildNumberEntities)) {
            throw new NoBuildNumberException($this->entitiesCollection);
        }

        if (count($buildNumberEntities) > 1) {
            throw new MultipleBuildNumbersException($this->entitiesCollection);
        }
    }

    private function extractBuildNumber(array $buildNumberEntities): Entity
    {
        return reset($buildNumberEntities);
    }

    /**
     * @throws AbstractActionException
     */
    private function validateEnvironment(array $environmentEntities): void
    {
        if (empty($environmentEntities)) {
            throw new NoEnvironmentException($this->entitiesCollection);
        }

        if (count($environmentEntities) > 1) {
            throw new MultipleEnvironmentsException($this->entitiesCollection);
        }
    }

    private function extractEnvironment(array $environmentEntities): Entity
    {
        return reset($environmentEntities);
    }

    /**
     * @throws AbstractActionException
     */
    private function validateMarket(array $marketEntities): void
    {
        if (count($marketEntities) > 1) {
            throw new MultipleMarketsException($this->entitiesCollection);
        }
    }

    private function extractMarket(array $marketEntities): Entity
    {
        $entity = reset($marketEntities);

        if ($entity instanceof Entity) {
            return $entity;
        }

        return new Entity(true, 100, 'urlaubspiratende', 'value');
    }
}
