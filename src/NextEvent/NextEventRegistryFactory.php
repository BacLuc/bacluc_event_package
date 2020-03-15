<?php


namespace BaclucEventPackage\NextEvent;


use BaclucC5Crud\Controller\ActionProcessor;
use BaclucC5Crud\Controller\ActionProcessors\ShowTable;
use BaclucC5Crud\Controller\ActionRegistry;

class NextEventRegistryFactory
{
    const SHOW_NEXT_EVENT = "show_next_event";

    /**
     * @var ActionProcessor[]
     */
    private $actions;

    public function __construct(
        ShowNextEvent $showLastEvent,
        ShowTable $showTable
    ) {
        $this->actions = func_get_args();
    }


    public function createActionRegistry(): ActionRegistry
    {
        return new ActionRegistry($this->actions);
    }
}