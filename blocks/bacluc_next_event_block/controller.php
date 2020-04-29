<?php

namespace Concrete\Package\BaclucEventPackage\Block\BaclucNextEventBlock;

use BaclucC5Crud\Adapters\Concrete5\Concrete5Renderer;
use BaclucC5Crud\Adapters\Concrete5\DIContainerFactory;
use BaclucC5Crud\Controller\ActionProcessor;
use BaclucC5Crud\Controller\ActionRegistry;
use BaclucC5Crud\Controller\ActionRegistryFactory;
use BaclucC5Crud\Controller\CrudController;
use BaclucC5Crud\Controller\Renderer;
use BaclucC5Crud\Controller\Validation\FieldValidator;
use BaclucC5Crud\Controller\Validation\IgnoreFieldForValidation;
use BaclucC5Crud\Controller\Validation\ValidationResult;
use BaclucC5Crud\Controller\Validation\ValidationResultItem;
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
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Page\Page;
use Concrete\Core\Routing\Redirect;
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


    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws ReflectionException
     */
    public function view()
    {
        $this->processAction($this->createCrudController()
                                  ->getActionFor(EventActionRegistryFactory::SHOW_NEXT_EVENT, $this->bID));
    }

    /**
     * @param $blockId
     * @param $editId
     * @throws DependencyException
     * @throws NotFoundException
     * @throws ReflectionException
     */
    public function action_cancel_form($blockId)
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
        $definitions[Renderer::class] =
            create(Concrete5Renderer::class)->constructor($this, $packageController->getPackagePath());
        $definitions[ActionRegistry::class] = factory(function (ContainerInterface $container) {
            return $container->get(EventActionRegistryFactory::class)->createActionRegistry();
        });
        $definitions[ViewActionRegistry::class] = factory([ViewActionRegistryFactory::class, "createActionRegistry"]);
        $definitions[TableViewEntrySupplier::class] = autowire(ShowNextEventEntrySupplier::class);
        $definitions[NoEditIdFallbackActionProcessor::class] = autowire(ShowNextEvent::class);
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
    public function action_show_cancel_event_form($blockId, $editId)
    {
        $this->processAction($this->createEventCancellationController()
                                  ->getActionFor(EventActionRegistryFactory::SHOW_CANCEL_EVENT_FORM, $blockId),
            $editId);
    }

    /**
     * @param $blockId
     * @param $editId
     * @throws DependencyException
     * @throws NotFoundException
     * @throws ReflectionException
     */
    public function action_post_cancel_event_form($blockId, $editId)
    {
        $this->processAction($this->createEventCancellationController()
                                  ->getActionFor(EventActionRegistryFactory::POST_CANCEL_EVENT_FORM, $blockId),
            $editId);
        if ($this->blockViewRenderOverride == null) {
            Redirect::page(Page::getCurrentPage())->send();
            exit();
        }
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
                             ->forType(FormField::class)
                             ->useFactory(DontShowFormField::create())
                             ->forType(FieldValidator::class)
                             ->useFactory(IgnoreFieldForValidation::create())
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
        $definitions[Renderer::class] =
            create(Concrete5Renderer::class)->constructor($this, $packageController->getPackagePath());
        $definitions[ActionRegistry::class] = factory(function (ContainerInterface $container) {
            return $container->get(EventActionRegistryFactory::class)->createActionRegistry();
        });
        $definitions[ViewActionRegistry::class] = factory([ViewActionRegistryFactory::class, "createActionRegistry"]);
        $definitions[NoEditIdFallbackActionProcessor::class] = autowire(ShowNextEvent::class);
        $definitions[SubmitFormViewAction::class] =
            factory([ViewActionRegistry::class, "getByName"])->parameter("name",
                EventActionRegistryFactory::POST_CANCEL_EVENT_FORM);
        $containerBuilder->addDefinitions($definitions);
        $container = $containerBuilder->build();
        return $container->get(CrudController::class);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function add()
    {
        $this->processAction($this->createConfigController()
                                  ->getActionFor(ActionRegistryFactory::ADD_NEW_ROW_FORM, $this->bID));
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function edit()
    {
        $this->processAction($this->createConfigController()
            ->getActionFor(ActionRegistryFactory::EDIT_ROW_FORM, $this->bID),
            $this->bID);
    }

    /**
     * @param array|string|null $args
     * @return bool|ErrorList|void
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function validate($args)
    {

        /** @var $validationResult ValidationResult */
        $validationResult = $this->processAction($this->createConfigController()
            ->getActionFor(ActionRegistryFactory::VALIDATE_FORM,
                $this->bID),
            $this->bID);
        /** @var $e ErrorList */
        $e = $this->app->make(ErrorList::class);
        foreach ($validationResult as $validationResultItem) {
            /** @var $validationResultItem ValidationResultItem */
            foreach ($validationResultItem->getMessages() as $message) {
                $e->add($validationResultItem->getName() . ": " . $message,
                    $validationResultItem->getName(),
                    $validationResultItem->getName());
            }
        }
        return $e;
    }

    /**
     * @param array $args
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function save($args)
    {
        parent::save($args);
        $this->processAction($this->createConfigController()
            ->getActionFor(ActionRegistryFactory::POST_FORM, $this->bID),
            $this->bID);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function delete()
    {
        parent::delete();
        $this->processAction($this->createConfigController()
            ->getActionFor(ActionRegistryFactory::DELETE_ENTRY, $this->bID),
            $this->bID);
    }

    /**
     * @return CrudController
     * @throws DependencyException
     * @throws NotFoundException
     * @throws Exception
     */
    private function createConfigController(): CrudController
    {
        $entityManager = PackageController::getEntityManagerStatic();
        $entityClass = NextEventConfiguration::class;

        $app = Application::getFacadeApplication();
        /** @var PackageController $packageController */
        $packageController = $app->make(PackageService::class)->getByHandle(EventPackageController::PACKAGE_HANDLE);

        $container = DIContainerFactory::createContainer($this,
            $entityManager,
            $entityClass,
            "",
            (new EntityFieldOverrideBuilder($entityClass))->build(),
            $this->bID,
            $packageController->getPackagePath(),
            FormType::$BLOCK_CONFIGURATION);
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
