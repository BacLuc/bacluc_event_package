<?php

namespace Concrete\Package\BaclucEventPackage\Block\BaclucNextEventBlock;

use BaclucC5Crud\Adapters\Concrete5\DIContainerFactory;
use BaclucC5Crud\Controller\ActionProcessor;
use BaclucC5Crud\Controller\ActionRegistry;
use BaclucC5Crud\Controller\CrudController;
use BaclucC5Crud\Entity\TableViewEntrySupplier;
use BaclucC5Crud\FieldConfigurationOverride\EntityFieldOverrideBuilder;
use BaclucC5Crud\View\FormType;
use BaclucEventPackage\Event;
use BaclucEventPackage\NextEvent\NextEventRegistryFactory;
use BaclucEventPackage\NextEvent\ShowNextEventEntrySupplier;
use Concrete\Core\Block\BlockController;
use Concrete\Package\BaclucC5Crud\Controller as PackageController;
use DI\ContainerBuilder;
use DI\DependencyException;
use DI\NotFoundException;
use Exception;
use Psr\Container\ContainerInterface;
use ReflectionException;
use function DI\autowire;
use function DI\factory;
use function DI\value;

class Controller extends BlockController
{


    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws ReflectionException
     */
    public function view()
    {
        $this->processAction($this->createCrudController()
                                  ->getActionFor(NextEventRegistryFactory::SHOW_NEXT_EVENT, $this->bID, $this->bID));
    }

    private function processAction(ActionProcessor $actionProcessor, ...$additionalParams)
    {
        return $actionProcessor->process($this->request->get(null) ?: [],
            $this->request->post(null) ?: [],
            array_key_exists(0, $additionalParams) ? $additionalParams[0] : null);
    }

    /**
     * @return CrudController
     * @throws DependencyException
     * @throws NotFoundException
     * @throws Exception
     * @throws ReflectionException
     */
    private function createCrudController(): CrudController
    {
        $entityManager = PackageController::getEntityManagerStatic();
        $entityClass = Event::class;
        $entityFieldOverrides = new EntityFieldOverrideBuilder($entityClass);

        $definitions = DIContainerFactory::createDefinition(
            $entityManager,
            $entityClass,
            $entityFieldOverrides->build(),
            FormType::$BLOCK_VIEW);

        $containerBuilder = new ContainerBuilder();
        $definitions[BlockController::class] = value($this);
        $definitions[ActionRegistry::class] = factory(function (ContainerInterface $container) {
            return $container->get(NextEventRegistryFactory::class)->createActionRegistry();
        });
        $definitions[TableViewEntrySupplier::class] = autowire(ShowNextEventEntrySupplier::class);
        $containerBuilder->addDefinitions($definitions);
        $container = $containerBuilder->build();
        return $container->get(CrudController::class);
    }

    /**
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return t("Show next Event of Group");
    }

    /**
     * @return string
     */
    public function getBlockTypeName()
    {
        return t("BaclucNextEventBlock");
    }


}
