<?php

namespace App\Entity\Repositories;
use App\Entity\DeviceMeasureValue;
use App\Entity\ExperimentDeviceMeasure;
use App\Entity\IdentifiedObject;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

class DeviceMeasureValueRepository implements IDependentSBaseRepository
{
	/** @var EntityManager * */
	private $em;

	/** @var \Doctrine\ORM\DeviceMeasureValueRepository */
	private $repository;

	/** @var ExperimentDeviceMeasure */
	private $deviceMeasure;

	public function __construct(EntityManager $em)
	{
		$this->em = $em;
		$this->repository = $em->getRepository(DeviceMeasureValue::class);
	}

	protected static function getParentClassName(): string
	{
		return ExperimentDeviceMeasure::class;
	}

	public function get(int $id)
	{
		return $this->em->find(DeviceMeasureValue::class, $id);
	}

	public function getNumResults(array $filter): int
	{
		return ((int)$this->buildListQuery($filter)
			->select('COUNT(v)')
			->getQuery()
			->getScalarResult());
	}

	public function getList(array $filter, array $sort, array $limit): array
	{
		$query = $this->buildListQuery($filter)
			->select('v.id, v.value');

        return $query->getQuery()->getArrayResult();
	}

	public function getParent(): IdentifiedObject
	{
		return $this->deviceMeasure;
	}

	public function setParent(IdentifiedObject $object): void
	{
		$className = static::getParentClassName();
		if (!($object instanceof $className))
			throw new \Exception('Parent of value must be ' . $className);
		$this->deviceMeasure = $object;
	}

	private function buildListQuery(array $filter): QueryBuilder
	{
		$query = $this->em->createQueryBuilder()
			->from(DeviceMeasureValue::class, 'v')
            ->where('v.measureId = :measureId')
            ->setParameters([
                'measureId' => $this->deviceMeasure->getId()
            ]);
		return $query;
	}

    /**
     * @param DeviceMeasureValue$object
     */
    public function add($object): void
    {
    }

    public function remove($object): void
    {
    }
}
