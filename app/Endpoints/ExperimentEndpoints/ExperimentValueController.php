<?php

namespace App\Controllers;

use App\Entity\{ExperimentValues,
    IdentifiedObject,
    Repositories\IEndpointRepository,
    Repositories\ExperimentRepository,
    Repositories\ExperimentVariableRepository,
    Repositories\ExperimentValueRepository,
    Repositories\UnitRepository};

use App\Exceptions\
{
	MissingRequiredKeyException,
	DependentResourcesBoundException
};
use App\Helpers\ArgumentParser;
use Slim\Container;
use Slim\Http\{
	Request, Response
};
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @property-read ExperimentValueRepository $repository
 * @method ExperimentValues getObject(int $id, IEndpointRepository $repository = null, string $objectName = null)
 */
final class ExperimentValueController extends ParentedRepositoryController
{

	/** @var ExperimentValueRepository */
	private $valueRepository;
    private $variableRepository;
    private $unitRepository;

	public function __construct(Container $c)
	{
		parent::__construct($c);
		$this->valueRepository = $c->get(ExperimentValueRepository::class);
        $this->variableRepository = $c->get(ExperimentVariableRepository::class);
        $this->unitRepository = $c->get(UnitRepository::class);
	}

	protected static function getAllowedSort(): array
	{
		return ['id', 'time', 'value'];
	}


	protected function getData(IdentifiedObject $value): array
	{
		/** @var ExperimentValues $value */
		return [
            'variable' => ['id' => $value->getVariableId()->getId(), 'name' => $value->getVariableId()->getName()],
            'unit' => ['id' => $value->getUnitId()->getId(), 'name' => $value->getUnitId()->getName()],
			'time' => $value->getTime(),
			'value' => $value->getValue(),
            'isAutomatic' => $value->getIsAutomatic(),
		];
	}

	protected function setData(IdentifiedObject $value, ArgumentParser $data): void
	{
		/** @var ExperimentValues $value */
		$value->getExperimentId() ?: $value->setExperimentId($this->repository->getParent());
        !$data->hasKey('variableId') ?: $value->setVariableId($this->variableRepository->get($data->getInt('variableId')));
        !$data->hasKey('unitId') ?: $value->setUnitId($this->unitRepository->get($data->getInt('unitId')));
		!$data->hasKey('time') ?: $value->setTime($data->getFloat('time'));
		!$data->hasKey('value') ?: $value->setValue($data->getFloat('value'));
        !$data->hasKey('isAutomatic') ?: $value->setIsAutomatic($data->getBool('isAutomatic'));
	}

	protected function createObject(ArgumentParser $body): IdentifiedObject
	{
        if (!$body->hasKey('variableId'))
            throw new MissingRequiredKeyException('variableId');
        if (!$body->hasKey('unitId'))
            throw new MissingRequiredKeyException('unitId');
		if (!$body->hasKey('time'))
			throw new MissingRequiredKeyException('time');
		if (!$body->hasKey('value'))
			throw new MissingRequiredKeyException('value');
        if (!$body->hasKey('isAutomatic'))
            throw new MissingRequiredKeyException('isAutomatic');
		return new ExperimentValues;
	}

	protected function checkInsertObject(IdentifiedObject $value): void
	{
		/** @var ExperimentValues $value */
		/*if ($value->getExperimentId() === null)
			throw new MissingRequiredKeyException('experimentId');*/
		if ($value->getVariableId() === null)
			throw new MissingRequiredKeyException('variableId');
        if ($value->getUnitId() === null)
            throw new MissingRequiredKeyException('unitId');
		if ($value->getTime() === null)
			throw new MissingRequiredKeyException('time');
		if ($value->getValue() === null)
			throw new MissingRequiredKeyException('value');
	}

	public function delete(Request $request, Response $response, ArgumentParser $args): Response
	{
		return parent::delete($request, $response, $args);
	}

	protected function getValidator(): Assert\Collection
	{
		return new Assert\Collection([
            /*'experimentId' => new Assert\Type(['type' => 'integer']),
            'variableId' => new Assert\Type(['type' => 'integer']),
            'unitId' => new Assert\Type(['type' => 'integer']),
            'value' => new Assert\Type(['type' => 'double']),
			'time' => new Assert\Type(['type' => 'double']),*/
		]);
	}

	protected static function getObjectName(): string
	{
		return 'value';
	}

	protected static function getRepositoryClassName(): string
	{
		return ExperimentValueRepository::Class;
	}

	protected static function getParentRepositoryClassName(): string
	{
		return ExperimentRepository::class;
	}

	protected function getParentObjectInfo(): array
	{
		return ['experiment-id', 'experiment'];
	}
}
