<?php


namespace BaclucEventPackage;


use BaclucC5Crud\Entity\TableViewEntrySupplier;

class EventCancellationsTableEntrySupplier implements TableViewEntrySupplier
{
    /**
     * @var int
     */
    private $eventId;
    /**
     * @var CancellationsRepository
     */
    private $cancellationsRepository;

    public function __construct(int $eventId, CancellationsRepository $cancellationsRepository)
    {
        $this->eventId = $eventId;
        $this->cancellationsRepository = $cancellationsRepository;
    }


    public function getEntries()
    {
        return $this->cancellationsRepository->getCancellationsOfEvent($this->eventId);
    }
}