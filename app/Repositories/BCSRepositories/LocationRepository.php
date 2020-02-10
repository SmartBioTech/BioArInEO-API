<?php

namespace App\Entity\Repositories;
use App\Entity\Location;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

class LocationRepository implements IEndpointRepository
{
    /** @var EntityManager * */
    protected $em;

    /** @var \Doctrine\ORM\LocationRepository */
    private $repository;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(Location::class);
    }

    public function get(int $id)
    {
        return $this->em->find(Location::class, $id);
    }

    public function getNumResults(array $filter): int
    {
        return ((int)$this->buildListQuery($filter)
            ->select('COUNT(l)')
            ->getQuery()
            ->getScalarResult());
    }

    public function getList(array $filter, array $sort, array $limit): array
    {
        $query = $this->buildListQuery($filter)
            ->select('l.id, l.description, l.longitude, l.latitude');
        return $query->getQuery()->getArrayResult();
    }

    private function buildListQuery(array $filter): QueryBuilder
    {
        $query = $this->em->createQueryBuilder()
            ->from(Location::class, 'l');
        return $query;
    }

}
