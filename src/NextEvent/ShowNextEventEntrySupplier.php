<?php

namespace BaclucEventPackage\NextEvent;

use BaclucC5Crud\Controller\PaginationConfiguration;
use BaclucC5Crud\Entity\ConfigurationSupplier;
use BaclucC5Crud\Entity\TableViewEntrySupplier;
use function BaclucC5Crud\Lib\collect as collect;
use BaclucEventPackage\EventRepository;

class ShowNextEventEntrySupplier implements TableViewEntrySupplier {
    /**
     * @var EventRepository
     */
    private $eventRepository;
    /**
     * @var ConfigurationSupplier
     */
    private $configurationSupplier;

    public function __construct(EventRepository $eventRepository, ConfigurationSupplier $configurationSupplier) {
        $this->eventRepository = $eventRepository;
        $this->configurationSupplier = $configurationSupplier;
    }

    public function getEntries(PaginationConfiguration $paginationConfiguration) {
        /** @var NextEventConfiguration $configuration */
        $configuration = $this->configurationSupplier->getConfiguration();

        return $this->eventRepository->getLastEventOfGroup(collect($configuration->showNextEventOfGroups)
            ->map(function ($group) {
                return $group->gID;
            })
            ->toArray());
    }

    public function count() {
        return 1;
    }
}
