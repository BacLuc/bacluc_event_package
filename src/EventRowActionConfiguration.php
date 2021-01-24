<?php

namespace BaclucEventPackage;

use BaclucC5Crud\Controller\DefaultRowActionConfiguration;
use BaclucC5Crud\Controller\RowActionConfiguration;
use BaclucC5Crud\View\ViewActionRegistry;

class EventRowActionConfiguration implements RowActionConfiguration {
    /**
     * @var ViewActionRegistry
     */
    private $viewActionRegistry;
    /**
     * @var DefaultRowActionConfiguration
     */
    private $defaultRowActionConfiguration;

    public function __construct(
        DefaultRowActionConfiguration $defaultRowActionConfiguration,
        ViewActionRegistry $viewActionRegistry
    ) {
        $this->viewActionRegistry = $viewActionRegistry;
        $this->defaultRowActionConfiguration = $defaultRowActionConfiguration;
    }

    /**
     * {@inheritDoc}
     */
    public function getActions(): array {
        $viewActionDefinitions = $this->defaultRowActionConfiguration->getActions();
        $viewActionDefinitions[] = $this->viewActionRegistry->getByName(EventActionRegistryFactory::SHOW_CANCELLATIONS);

        return $viewActionDefinitions;
    }
}
