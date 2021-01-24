<?php

namespace Concrete\Package\BaclucEventPackage\Block\BaclucNextEventBlock;

use BaclucC5Crud\Adapters\Concrete5\Concrete5BlockConfigController;
use BaclucC5Crud\Adapters\Concrete5\Concrete5CurrentUrlSupplier;
use BaclucC5Crud\Adapters\Concrete5\Concrete5Renderer;
use BaclucC5Crud\Adapters\Concrete5\DIContainerFactory;
use BaclucC5Crud\Controller\ActionProcessor;
use BaclucC5Crud\Controller\ActionRegistry;
use BaclucC5Crud\Controller\CrudController;
use BaclucC5Crud\Controller\CurrentUrlSupplier;
use BaclucC5Crud\Controller\Renderer;
use BaclucC5Crud\Controller\Validation\FieldValidator;
use BaclucC5Crud\Controller\Validation\IgnoreFieldForValidation;
use BaclucC5Crud\Entity\TableViewEntrySupplier;
use BaclucC5Crud\FieldConfigurationOverride\EntityFieldOverrideBuilder;
use BaclucC5Crud\View\FormType;
use BaclucC5Crud\View\FormView\DontShowFormField;
use BaclucC5Crud\View\FormView\Field as FormField;
use BaclucC5Crud\View\SubmitFormViewAction;
use BaclucC5Crud\View\ViewActionRegistry;
use BaclucEventPackage\Event;
use BaclucEventPackage\EventActionRegistryFactory;
use BaclucEventPackage\EventCancellation;
use BaclucEventPackage\NextEvent\NextEventConfiguration;
use BaclucEventPackage\NextEvent\ShowNextEvent;
use BaclucEventPackage\NextEvent\ShowNextEventEntrySupplier;
use BaclucEventPackage\NoEditIdFallbackActionProcessor;
use BaclucEventPackage\ViewActionRegistryFactory;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Page\Page;
use Concrete\Core\Routing\Redirect;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Package\BaclucC5Crud\Controller as PackageController;
use Concrete\Package\BaclucEventPackage\Controller as EventPackageController;
use function DI\autowire;
use DI\ContainerBuilder;
use function DI\create;
use DI\DependencyException;
use function DI\factory;
use DI\NotFoundException;
use function DI\value;
use Exception;
use Psr\Container\ContainerInterface;
use ReflectionException;

class Controller extends BlockController {
    use Concrete5BlockConfigController;

    /**
     * Controller constructor.
     *
     * @param null|mixed $obj
     */
    public function __construct($obj = null) {
        parent::__construct($obj);
        $this->initializeConfig($this, [$this, 'createConfigController'], $this->bID);

        $app = Facade::getFacadeApplication();
        /** @var Localization $localisation */
        $localization = $app->make('Concrete\Core\Localization\Localization');
        $localization->setLocale('de_CH');
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws ReflectionException
     */
    public function view() {
        $this->processAction($this->createCrudController()
            ->getActionFor(EventActionRegistryFactory::SHOW_NEXT_EVENT, $this->bID));
    }

    /**
     * @param $blockId
     * @param $editId
     *
     * @throws DependencyException
     * @throws NotFoundException
     * @throws ReflectionException
     */
    public function action_cancel_form($blockId) {
        $this->view();
    }

    /**
     * @param $blockId
     * @param $editId
     *
     * @throws DependencyException
     * @throws NotFoundException
     * @throws ReflectionException
     */
    public function action_show_cancel_event_form($blockId, $editId) {
        if ($blockId != $this->bID) {
            return $this->view();
        }
        $this->processAction(
            $this->createEventCancellationController()
                ->getActionFor(EventActionRegistryFactory::SHOW_CANCEL_EVENT_FORM, $blockId),
            $editId
        );
    }

    /**
     * @param $blockId
     * @param $editId
     *
     * @throws DependencyException
     * @throws NotFoundException
     * @throws ReflectionException
     */
    public function action_post_cancel_event_form($blockId, $editId) {
        $this->processAction(
            $this->createEventCancellationController()
                ->getActionFor(EventActionRegistryFactory::POST_CANCEL_EVENT_FORM, $blockId),
            $editId
        );
        if (null == $this->blockViewRenderOverride) {
            Redirect::page(Page::getCurrentPage())->send();

            exit();
        }
    }

    /**
     * @return string
     */
    public function getBlockTypeDescription() {
        return t('Show next Event of Group');
    }

    /**
     * @return string
     */
    public function getBlockTypeName() {
        return t('BaclucNextEventBlock');
    }

    private function processAction(ActionProcessor $actionProcessor, ...$additionalParams) {
        return $actionProcessor->process(
            $this->request->query->all() ?: [],
            $this->request->post(null) ?: [],
            array_key_exists(0, $additionalParams) ? $additionalParams[0] : null
        );
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws Exception
     * @throws ReflectionException
     */
    private function createCrudController(): CrudController {
        $entityManager = PackageController::getEntityManagerStatic();
        $entityClass = Event::class;
        $entityFieldOverrides = new EntityFieldOverrideBuilder($entityClass);

        $definitions = DIContainerFactory::createDefinition(
            $entityManager,
            $entityClass,
            NextEventConfiguration::class,
            $entityFieldOverrides->build(),
            $this->bID,
            FormType::$BLOCK_VIEW
        );

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
        $definitions[ViewActionRegistry::class] = factory([ViewActionRegistryFactory::class, 'createActionRegistry']);
        $definitions[TableViewEntrySupplier::class] = autowire(ShowNextEventEntrySupplier::class);
        $definitions[NoEditIdFallbackActionProcessor::class] = autowire(ShowNextEvent::class);
        $containerBuilder->addDefinitions($definitions);
        $container = $containerBuilder->build();

        return $container->get(CrudController::class);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws Exception
     * @throws ReflectionException
     */
    private function createEventCancellationController(): CrudController {
        $entityManager = PackageController::getEntityManagerStatic();
        $entityClass = EventCancellation::class;
        $entityFieldOverrides = new EntityFieldOverrideBuilder($entityClass);

        $entityFieldOverrides->forField('event')
            ->forType(FormField::class)
            ->useFactory(DontShowFormField::create())
            ->forType(FieldValidator::class)
            ->useFactory(IgnoreFieldForValidation::create())
            ->buildField()
        ;

        $definitions = DIContainerFactory::createDefinition(
            $entityManager,
            $entityClass,
            NextEventConfiguration::class,
            $entityFieldOverrides->build(),
            $this->bID,
            FormType::$BLOCK_VIEW
        );

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
        $definitions[ViewActionRegistry::class] = factory([ViewActionRegistryFactory::class, 'createActionRegistry']);
        $definitions[NoEditIdFallbackActionProcessor::class] = autowire(ShowNextEvent::class);
        $definitions[SubmitFormViewAction::class] =
            factory([ViewActionRegistry::class, 'getByName'])->parameter(
                'name',
                EventActionRegistryFactory::POST_CANCEL_EVENT_FORM
            );
        $containerBuilder->addDefinitions($definitions);
        $container = $containerBuilder->build();

        return $container->get(CrudController::class);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws Exception
     */
    private function createConfigController(): CrudController {
        $entityManager = PackageController::getEntityManagerStatic();
        $entityClass = NextEventConfiguration::class;

        $app = Application::getFacadeApplication();
        /** @var PackageController $packageController */
        $packageController = $app->make(PackageService::class)->getByHandle(EventPackageController::PACKAGE_HANDLE);

        $container = DIContainerFactory::createContainer(
            $this,
            $entityManager,
            $entityClass,
            '',
            (new EntityFieldOverrideBuilder($entityClass))->build(),
            $this->bID,
            $packageController->getPackagePath(),
            FormType::$BLOCK_CONFIGURATION
        );

        return $container->get(CrudController::class);
    }
}
