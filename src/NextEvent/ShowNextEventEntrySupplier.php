<?php


namespace BaclucEventPackage\NextEvent;


use BaclucC5Crud\Entity\TableViewEntrySupplier;
use BaclucEventPackage\EventRepository;

class ShowNextEventEntrySupplier implements TableViewEntrySupplier
{
    /**
     * @var EventRepository
     */
    private $eventRepository;


    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function getEntries()
    {
        return $this->eventRepository->getLastEventOfGroup([1, 2, 3]);
    }
}