<?php


namespace BaclucEventPackage;


use BaclucC5Crud\Entity\Repository;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class EventRepository implements Repository
{
    /**
     * @var Repository
     */
    private $standardRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;


    /**
     * EventRepository constructor.
     * @param Repository $standardRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(Repository $standardRepository, EntityManager $entityManager)
    {
        $this->standardRepository = $standardRepository;
        $this->entityManager = $entityManager;
    }


    public function create()
    {
        return $this->standardRepository->create();
    }

    public function persist($entity)
    {
        return $this->standardRepository->persist($entity);
    }

    /**
     * @inheritDoc
     */
    public function getAll()
    {
        return $this->standardRepository->getAll();
    }

    public function getById(int $id)
    {
        return $this->standardRepository->getById($id);
    }

    public function delete($toDeleteEntity)
    {
        return $this->standardRepository->delete($toDeleteEntity);
    }

    public function getLastEventOfGroup(array $groupIds)
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('event')
           ->from(Event::class, "event")
           ->join("event.eventGroups", "groups")
           ->where($qb->expr()->in("groups.gID", ":groupIds"))
           ->andWhere($qb->expr()->gte("event.date_to", ":date"))
           ->orderBy('event.date_from')
           ->setMaxResults(1)
           ->setParameter("groupIds", $groupIds)
           ->setParameter("date", new DateTime());
        $query = $qb->getQuery();
        return $query->getResult();
    }
}