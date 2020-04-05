<?php


namespace BaclucEventPackage;


use BaclucC5Crud\Controller\ActionProcessor;
use BaclucC5Crud\Controller\ActionRegistry;
use BaclucC5Crud\Controller\ActionRegistryFactory;
use BaclucEventPackage\NextEvent\ShowNextEvent;

class EventActionRegistryFactory
{
    const SHOW_NEXT_EVENT        = "show_next_event";
    const SHOW_CANCEL_EVENT_FORM = "show_cancel_event_form";
    const POST_CANCEL_EVENT_FORM = "post_cancel_event_form";

    /**
     * @var ActionProcessor[]
     */
    private $actions;

    public function __construct(
        ActionRegistryFactory $actionRegistryFactory,
        ShowNextEvent $showNextEvent,
        ShowCancelEventForm $showCancelEventForm,
        PostCancelEventForm $postCancelEventForm
    ) {

        $this->actions = $actionRegistryFactory->createActionRegistry()->getActions();
        $this->actions[] = $showNextEvent;
        $this->actions[] = $showCancelEventForm;
        $this->actions[] = $postCancelEventForm;
    }


    public function createActionRegistry(): ActionRegistry
    {
        return new ActionRegistry($this->actions);
    }
}