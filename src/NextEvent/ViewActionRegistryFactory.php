<?php


namespace BaclucEventPackage\NextEvent;


use BaclucC5Crud\View\ViewActionDefinition;
use BaclucC5Crud\View\ViewActionRegistry;
use BaclucEventPackage\EventActionRegistryFactory;

class ViewActionRegistryFactory
{
    public function createActionRegistry(): ViewActionRegistry
    {
        $actions = [
            new ViewActionDefinition(EventActionRegistryFactory::SHOW_CANCEL_EVENT_FORM,
                "cancel-event",
                "cancel",
                "cancel",
                "fa-sign-out"),
        ];
        return new ViewActionRegistry($actions);
    }
}