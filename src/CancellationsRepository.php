<?php

namespace BaclucEventPackage;

use BaclucC5Crud\Entity\Identifiable;
use BaclucC5Crud\Entity\OrderConfigEntry;
use BaclucC5Crud\Entity\Repository;
use BaclucEventPackage\Entity\EventCancellation;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class CancellationsRepository implements Repository {
    /**
     * @var Repository
     */
    private $standardRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(Repository $standardRepository, EntityManager $entityManager) {
        $this->standardRepository = $standardRepository;
        $this->entityManager = $entityManager;
    }

    public function create() {
        return $this->standardRepository->create();
    }

    public function persist(Identifiable $entity) {
        return $this->standardRepository->persist($entity);
    }

    public function getAll(int $offset = 0, int $limit = null, array $orderEntries = []) {
        if (0 == sizeof($orderEntries)) {
            $orderEntries = [new OrderConfigEntry('name')];
        }

        return $this->standardRepository->getAll($offset, $limit, $orderEntries);
    }

    public function getById(int $id) {
        return $this->standardRepository->getById($id);
    }

    public function delete(Identifiable $toDeleteEntity) {
        return $this->standardRepository->delete($toDeleteEntity);
    }

    public function getCancellationsOfEvent(int $eventId, int $offset, int $limit) {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('cancellation')
            ->from(EventCancellation::class, 'cancellation')
            ->join('cancellation.event', 'event')
            ->where($qb->expr()->eq('event.id', ':eventId'))
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->orderBy('cancellation.name')
            ->setParameter('eventId', $eventId)
        ;
        $query = $qb->getQuery();

        return $query->getResult();
    }

    public function countCancellationsOfEvent(int $eventId) {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('count(cancellation)')
            ->from(EventCancellation::class, 'cancellation')
            ->join('cancellation.event', 'event')
            ->where($qb->expr()->eq('event.id', ':eventId'))
            ->orderBy('cancellation.name')
            ->setParameter('eventId', $eventId)
        ;
        $query = $qb->getQuery();

        try {
            return $query->getSingleScalarResult();
        } catch (NonUniqueResultException|NoResultException $e) {
            throw new \RuntimeException('Error getting count of result '.$e->getMessage());
        }
    }

    public function count() {
        $this->standardRepository->count();
    }
}
