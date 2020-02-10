<?php

namespace App\Entity\Repositories;

use App\Entity\Experiment;
use App\Entity\ExperimentDeviceMeasure;
use App\Entity\IdentifiedObject;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

class ExperimentDeviceMeasureRepository implements IDependentSBaseRepository
{
	/** @var EntityManager * */
	private $em;

	/** @var \Doctrine\ORM\MeasureRepository */
	private $repository;

	/** @var Experiment */
	private $experiment;

	public function __construct(EntityManager $em)
	{
		$this->em = $em;
		$this->repository = $em->getRepository(ExperimentDeviceMeasure::class);
	}

	protected static function getParentClassName(): string
	{
		return Experiment::class;
	}

	public function get(int $id)
	{
		return $this->em->find(ExperimentDeviceMeasure::class, $id);
	}

	public function getNumResults(array $filter): int
	{
		return ((int)$this->buildListQuery($filter)
			->select('COUNT(c)')
			->getQuery()
			->getScalarResult());
	}

	public function getList(array $filter, array $sort, array $limit): array
	{
        //return $this->em->createQuery('SELECT IDENTITY(c.variableId) AS variable_id from Variable')->getArrayResult();
		$query = $this->buildListQuery($filter)
			->select('c.id, c.time');

        return $query->getQuery()->getArrayResult();
	}

	public function getParent(): IdentifiedObject
	{
		return $this->experiment;
	}

	public function setParent(IdentifiedObject $object): void
	{
		$className = static::getParentClassName();
		if (!($object instanceof $className))
			throw new \Exception('Parent of device measure must be ' . $className);
		$this->experiment = $object;
	}

	private function buildListQuery(array $filter): QueryBuilder
	{
		$query = $this->em->createQueryBuilder()
			->from(ExperimentDeviceMeasure::class, 'c')
            ->where('c.experimentId = :experimentId')
            ->setParameters([
                'experimentId' => $this->experiment->getId()
            ]);
		return $query;
	}

    /**
     * @param Experiment$object
     */
    public function add($object): void
    {
    }

    public function remove($object): void
    {
    }
}
