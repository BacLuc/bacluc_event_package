<?php

namespace BaclucEventPackage;

use BaclucC5Crud\Controller\PaginationConfiguration;
use BaclucC5Crud\Entity\TableViewEntrySupplier;

class EventCancellationsTableEntrySupplier implements TableViewEntrySupplier {
    /**
     * @var int
     */
    private $eventId;
    /**
     * @var CancellationsRepository
     */
    private $cancellationsRepository;

    public function __construct(int $eventId, CancellationsRepository $cancellationsRepository) {
        $this->eventId = $eventId;
        $this->cancellationsRepository = $cancellationsRepository;
    }

    public function getEntries(PaginationConfiguration $paginationConfiguration) {
        return $this->cancellationsRepository->getCancellationsOfEvent(
            $this->eventId,
            $paginationConfiguration->getOffset(),
            $paginationConfiguration->getPageSize()
        );
    }

    public function count() {
        return $this->cancellationsRepository->countCancellationsOfEvent($this->eventId);
    }
}
