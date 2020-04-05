<?php


namespace BaclucEventPackage\NextEvent;


use BaclucC5Crud\View\ViewActionDefinition;
use BaclucC5Crud\View\ViewActionRegistry;
use BaclucC5Crud\View\ViewActionRegistryFactory as CrudViewActionRegistryFactory;
use BaclucEventPackage\EventActionRegistryFactory;

class ViewActionRegistryFactory
{
    /**
     * @var CrudViewActionRegistryFactory
     */
    private $viewActionRegistryFactory;

    public function __construct(CrudViewActionRegistryFactory $viewActionRegistryFactory)
    {
        $this->viewActionRegistryFactory = $viewActionRegistryFactory;
    }

    public function createActionRegistry(): ViewActionRegistry
    {
        $viewActionRegistry = $this->viewActionRegistryFactory->createActionRegistry();
        $existingActions = $viewActionRegistry->getActions();

        $existingActions[] =
            new ViewActionDefinition(EventActionRegistryFactory::SHOW_CANCEL_EVENT_FORM,
                "cancel-event",
                "cancel",
                "cancel",
                "fa-sign-out");
        $existingActions[] =
            new ViewActionDefinition(EventActionRegistryFactory::POST_CANCEL_EVENT_FORM,
                "",
                "Save Cancellation",
                "Save Cancellation",
                "");
        return new ViewActionRegistry($existingActions);
    }
}