<?php

namespace Concrete\Package\BaclucEventPackage\Block\BaclucEventBlock;

use BaclucC5Crud\Adapters\Concrete5\Concrete5CrudController;
use BaclucC5Crud\Adapters\Concrete5\Concrete5CurrentUrlSupplier;
use BaclucC5Crud\Adapters\Concrete5\Concrete5Renderer;
use BaclucC5Crud\Adapters\Concrete5\DIContainerFactory;
use BaclucC5Crud\Controller\ActionProcessor;
use BaclucC5Crud\Controller\ActionRegistry;
use BaclucC5Crud\Controller\CrudController;
use BaclucC5Crud\Controller\CurrentUrlSupplier;
use BaclucC5Crud\Controller\Renderer;
use BaclucC5Crud\Controller\RowActionConfiguration;
use BaclucC5Crud\FieldConfigurationOverride\EntityFieldOverrideBuilder;
use BaclucC5Crud\View\FormType;
use BaclucC5Crud\View\TableView\DontShowTableField;
use BaclucC5Crud\View\TableView\Field as TableField;
use BaclucC5Crud\View\ViewActionRegistry;
use BaclucEventPackage\Event;
use BaclucEventPackage\EventActionRegistryFactory;
use BaclucEventPackage\EventCancellation;
use BaclucEventPackage\EventRowActionConfiguration;
use BaclucEventPackage\NextEvent\NextEventConfiguration;
use BaclucEventPackage\NoEditIdFallbackActionProcessor;
use BaclucEventPackage\ShowErrorActionProcessor;
use BaclucEventPackage\ViewActionRegistryFactory;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Support\Facade\Application;
use Concrete\Package\BaclucC5Crud\Controller as PackageController;
use Concrete\Package\BaclucEventPackage\Controller as EventPackageController;
use DI\ContainerBuilder;
use DI\DependencyException;
use DI\NotFoundException;
use Exception;
use Psr\Container\ContainerInterface;
use ReflectionException;
use function DI\autowire;
use function DI\create;
use function DI\factory;
use function DI\value;

class Controller extends BlockController
{
    use Concrete5CrudController;

    /**
     * Controller constructor.
     */
    public function __construct($obj = null)
    {
        parent::__construct($obj);
        $this->initializeCrud($this, [$this, "createCrudController"], $this->bID);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws ReflectionException
     */
    public function action_view()
    {
        $this->view();
    }


    private function processAction(ActionProcessor $actionProcessor, ...$additionalParams)
    {
        return $actionProcessor->process($this->request->query->all() ?: [],
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

        $app = Application::getFacadeApplication();
        /** @var PackageController $packageController */
        $packageController = $app->make(PackageService::class)->getByHandle(EventPackageController::PACKAGE_HANDLE);

        $containerBuilder = new ContainerBuilder();
        $definitions =
            DIContainerFactory::createDefinition($entityManager,
                $entityClass,
                "",
                $entityFieldOverrides->build(),
                $this->bID,
                FormType::$BLOCK_VIEW);
        $definitions[BlockController::class] = value($this);
        $definitions[CurrentUrlSupplier::class] = autowire(Concrete5CurrentUrlSupplier::class);
        $definitions[Renderer::class] =
            create(Concrete5Renderer::class)->constructor($this, $packageController->getPackagePath());
        $definitions[ViewActionRegistry::class] = factory([ViewActionRegistryFactory::class, "createActionRegistry"]);
        $definitions[RowActionConfiguration::class] = autowire(EventRowActionConfiguration::class);
        $containerBuilder->addDefinitions($definitions);
        $container = $containerBuilder->build();
        return $container->get(CrudController::class);
    }

    /**
     * @param $blockId
     * @param $editId
     * @throws DependencyException
     * @throws NotFoundException
     * @throws ReflectionException
     */
    public function action_show_cancellations($blockId, $editId) {
        $this->processAction($this->createEventCancellationController()
            ->getActionFor(EventActionRegistryFactory::SHOW_CANCELLATIONS, $blockId),
            $editId);
    }

    /**
     * @return CrudController
     * @throws DependencyException
     * @throws NotFoundException
     * @throws Exception
     * @throws ReflectionException
     */
    private function createEventCancellationController(): CrudController
    {
        $entityManager = PackageController::getEntityManagerStatic();
        $entityClass = EventCancellation::class;
        $entityFieldOverrides = new EntityFieldOverrideBuilder($entityClass);

        $entityFieldOverrides->forField("event")
            ->forType(TableField::class)
            ->useFactory(DontShowTableField::create())
            ->buildField();

        $definitions = DIContainerFactory::createDefinition(
            $entityManager,
            $entityClass,
            NextEventConfiguration::class,
            $entityFieldOverrides->build(),
            $this->bID,
            FormType::$BLOCK_VIEW);

        $app = Application::getFacadeApplication();
        /** @var PackageController $packageController */
        $packageController = $app->make(PackageService::class)->getByHandle(EventPackageController::PACKAGE_HANDLE);
        $containerBuilder = new ContainerBuilder();
        $definitions[BlockController::class] = value($this);
        $definitions[CurrentUrlSupplier::class] = autowire(Concrete5CurrentUrlSupplier::class);
        $definitions[Renderer::class] =
            create(Concrete5Renderer::class)->constructor($this, $packageController->getPackagePath());
        $definitions[ActionRegistry::class] = factory(function (ContainerInterface $container) {
            return $container->get(EventActionRegistryFactory::class)->createActionRegistry();
        });
        $definitions[NoEditIdFallbackActionProcessor::class] = autowire(ShowErrorActionProcessor::class);
        $containerBuilder->addDefinitions($definitions);
        $container = $containerBuilder->build();
        return $container->get(CrudController::class);
    }

    /**
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return t("Create, Edit or Delete Events");
    }

    /**
     * @return string
     */
    public function getBlockTypeName()
    {
        return t("BaclucEventBlock");
    }


}
