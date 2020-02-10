<?php

namespace App\Controllers;

use App\Entity\{DeviceMeasureValue,
    ExperimentDeviceMeasure,
    ExperimentValues,
    IdentifiedObject,
    Repositories\DeviceRepository,
    Repositories\ExperimentDeviceMeasureRepository,
    Repositories\IEndpointRepository,
    Repositories\ExperimentRepository,
    Repositories\ExperimentValueRepository,
    Repositories\LocationRepository};

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
 * @property-read ExperimentDeviceMeasureRepository $repository
 * @method ExperimentDeviceMeasure getObject(int $id, IEndpointRepository $repository = null, string $objectName = null)
 */
final class ExperimentDeviceMeasureController extends ParentedRepositoryController
{

	/** @var ExperimentDeviceMeasureRepository */
	private $valueRepository;
    private $locationRepository;
    private $deviceRepository;

	public function __construct(Container $c)
	{
		parent::__construct($c);
		$this->valueRepository = $c->get(ExperimentValueRepository::class);
        $this->locationRepository = $c->get(LocationRepository::class);
        $this->deviceRepository = $c->get(DeviceRepository::class);
	}

	protected static function getAllowedSort(): array
	{
		return ['id', 'experimentId', 'deviceId', 'locationId', 'time'];
	}


	protected function getData(IdentifiedObject $deviceMeasure): array
	{
		/** @var ExperimentDeviceMeasure $deviceMeasure */
		return [
            'experiment' => ['id' => $deviceMeasure->getExperimentId()->getId(), 'name' => $deviceMeasure->getExperimentId()->getName()],
            'device' => ['id' => $deviceMeasure->getDeviceId()->getId(), 'name' => $deviceMeasure->getDeviceId()->getName()],
            'location' => ['id' => $deviceMeasure->getLocationId()->getId(), 'description' => $deviceMeasure->getLocationId()->getDescription()],
			'time' => $deviceMeasure->getTime(),
            'values' => $deviceMeasure->getValues()->map(function (DeviceMeasureValue $value) {
                return ['id' => $value->getId(),  'variable' => ['id' => $value->getVariableId()->getId(), 'name' => $value->getVariableId()->getName()],'unit' => $value->getUnitId()!= null ? UnitController::getData($value->getUnitId()):null, 'value' => $value->getValue()];
            })->toArray(),
		];
	}

	protected function setData(IdentifiedObject $deviceMeasure, ArgumentParser $data): void
	{
		/** @var ExperimentDeviceMeasure $deviceMeasure */
		$deviceMeasure->getExperimentId() ?: $deviceMeasure->setExperimentId($this->repository->getParent());
        !$data->hasKey('deviceId') ?: $deviceMeasure->setDeviceId($this->deviceRepository->get($data->getInt('deviceId')));
        !$data->hasKey('locationId') ?: $deviceMeasure->setLocationId($this->locationRepository->get($data->getInt('locationId')));
		!$data->hasKey('time') ?: $deviceMeasure->setTime($data->getString('time'));
	}

	protected function createObject(ArgumentParser $body): IdentifiedObject
	{
        if (!$body->hasKey('deviceId'))
            throw new MissingRequiredKeyException('deviceId');
        if (!$body->hasKey('locationId'))
            throw new MissingRequiredKeyException('locationId');
		if (!$body->hasKey('time'))
			throw new MissingRequiredKeyException('time');
		return new ExperimentDeviceMeasure();
	}

	protected function checkInsertObject(IdentifiedObject $deviceMeasure): void
	{
		/** @var ExperimentDeviceMeasure $deviceMeasure */
		if ($deviceMeasure->getExperimentId() === null)
			throw new MissingRequiredKeyException('experimentId');
        if ($deviceMeasure->getLocationId() === null)
            throw new MissingRequiredKeyException('locationId');
		if ($deviceMeasure->getTime() === null)
			throw new MissingRequiredKeyException('time');
		if ($deviceMeasure->getDeviceId() === null)
			throw new MissingRequiredKeyException('deviceId');
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
		return 'deviceMeasure';
	}

	protected static function getRepositoryClassName(): string
	{
		return ExperimentDeviceMeasureRepository::Class;
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
