<?php

namespace App\Controllers;

use App\Entity\{DeviceMeasureValue,
    ExperimentDeviceMeasure,
    ExperimentValues,
    ExperimentVariable,
    Experiment,
    IdentifiedObject,
    Repositories\DeviceMeasureValueRepository,
    Repositories\ExperimentDeviceMeasureRepository,
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
 * @property-read DeviceMeasureValueRepository $repository
 * @method DeviceMeasureValue getObject(int $id, IEndpointRepository $repository = null, string $objectName = null)
 */
final class DeviceMeasureValueController extends ParentedRepositoryController
{

	/** @var DeviceMeasureValueRepository */
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
		return ['id', 'value', 'variableId', 'unitId'];
	}


	protected function getData(IdentifiedObject $value): array
	{
		/** @var DeviceMeasureValue $value */
		return [
            'variable' => ['id' => $value->getVariableId()->getId(), 'name' => $value->getVariableId()->getName()],
            'unit' => ['id' => $value->getUnitId()->getId(), 'name' => $value->getUnitId()->getName()],
			'value' => $value->getValue(),
		];
	}

	protected function setData(IdentifiedObject $value, ArgumentParser $data): void
	{
		/** @var DeviceMeasureValue $value */
		$value->getMeasureId() ?: $value->setMeasureId($this->repository->getParent());
        !$data->hasKey('variableId') ?: $value->setVariableId($this->variableRepository->get($data->getInt('variableId')));
        !$data->hasKey('unitId') ?: $value->setUnitId($this->unitRepository->get($data->getInt('unitId')));
		!$data->hasKey('value') ?: $value->setValue($data->getFloat('value'));
	}

	protected function createObject(ArgumentParser $body): IdentifiedObject
	{
        if (!$body->hasKey('variableId'))
            throw new MissingRequiredKeyException('variableId');
        if (!$body->hasKey('unitId'))
            throw new MissingRequiredKeyException('unitId');
		if (!$body->hasKey('value'))
			throw new MissingRequiredKeyException('value');
		return new DeviceMeasureValue();
	}

	protected function checkInsertObject(IdentifiedObject $value): void
	{
		/** @var DeviceMeasureValue $value */
		if ($value->getVariableId() === null)
			throw new MissingRequiredKeyException('variableId');
        if ($value->getUnitId() === null)
            throw new MissingRequiredKeyException('unitId');
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
		return DeviceMeasureValueRepository::Class;
	}

	protected static function getParentRepositoryClassName(): string
	{
		return ExperimentDeviceMeasureRepository::class;
	}

	protected function getParentObjectInfo(): array
	{
		return ['measure-id', 'measure'];
	}
}
