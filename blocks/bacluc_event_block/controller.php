<?php
namespace Concrete\Package\BaclucEventPackage\Block\BaclucEventBlock;

use BaclucC5Crud\Adapters\Concrete5\DIContainerFactory;
use BaclucC5Crud\Controller\ActionProcessor;
use BaclucC5Crud\Controller\ActionRegistryFactory;
use BaclucC5Crud\Controller\CrudController;
use BaclucC5Crud\FieldConfigurationOverride\EntityFieldOverrideBuilder;
use BaclucC5Crud\View\FormType;
use BaclucEventPackage\Event;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Page\Page;
use Concrete\Core\Routing\Redirect;
use Concrete\Package\BaclucC5Crud\Controller as PackageController;
use DI\DependencyException;
use DI\NotFoundException;
use Exception;
use ReflectionException;

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
                                  ->getActionFor(ActionRegistryFactory::SHOW_TABLE, $this->bID));
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws ReflectionException
     */
    public function action_add_new_row_form($blockId)
    {
        $this->processAction($this->createCrudController()
                                  ->getActionFor(ActionRegistryFactory::ADD_NEW_ROW_FORM, $blockId));
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws ReflectionException
     */
    public function action_edit_row_form($blockId, $editId)
    {
        $this->processAction($this->createCrudController()
                                  ->getActionFor(ActionRegistryFactory::EDIT_ROW_FORM, $blockId),
            $editId);
    }

    /**
     * Attention: all action method are called twice.
     * Because this is a form submission, we stop after the function is executed
     * @throws DependencyException
     * @throws NotFoundException
     * @throws ReflectionException
     */
    public function action_post_form($blockId, $editId = null)
    {
        $this->processAction($this->createCrudController()
                                  ->getActionFor(ActionRegistryFactory::POST_FORM, $blockId),
            $editId);
        if ($this->blockViewRenderOverride == null) {
            Redirect::page(Page::getCurrentPage())->send();
            exit();
        }
    }

    /**
     * @param $ignored
     * @param $toDeleteId
     * @throws DependencyException
     * @throws NotFoundException
     * @throws ReflectionException
     */
    public function action_delete_entry($blockId, $toDeleteId)
    {
        $this->processAction($this->createCrudController()
                                  ->getActionFor(ActionRegistryFactory::DELETE_ENTRY, $blockId),
            $toDeleteId);
        if ($this->blockViewRenderOverride == null) {
            Redirect::page(Page::getCurrentPage())->send();
            exit();
        }
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function action_cancel_form($blockId)
    {
        $this->processAction($this->createCrudController()
                                  ->getActionFor(ActionRegistryFactory::SHOW_TABLE, $blockId));
    }

    /**
     * @param $ignored
     * @param $toShowId
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function action_show_details($blockId, $toShowId)
    {
        $this->processAction($this->createCrudController()
                                  ->getActionFor(ActionRegistryFactory::SHOW_ENTRY_DETAILS, $blockId),
            $toShowId);
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

        $container = DIContainerFactory::createContainer($this,
            $entityManager,
            $entityClass,
            "",
            $entityFieldOverrides->build(),
            $this->bID,
            FormType::$BLOCK_VIEW);
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
