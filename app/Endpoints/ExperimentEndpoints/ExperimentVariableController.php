<?php

namespace App\Controllers;

use App\Entity\{
    ExperimentValues,
    ExperimentVariable,
    IdentifiedObject,
    Repositories\DeviceRepository,
    Repositories\ExperimentRepository,
    Repositories\ExperimentVariableRepository,
    Repositories\IEndpointRepository};
use App\Exceptions\{
    DependentResourcesBoundException,
    MissingRequiredKeyException
};
use App\Helpers\ArgumentParser;
use Slim\Container;
use Slim\Http\{
    Request, Response
};
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @property-read Repository $repository
 * @method ExperimentVariable getObject(int $id, IEndpointRepository $repository = null, string $objectName = null)
 */
final class ExperimentVariableController extends WritableRepositoryController
{
    /** @var ExperimentVariableRepository */

    public function __construct(Container $c)
    {
        parent::__construct($c);
    }

    protected static function getAllowedSort(): array
    {
        return ['id, name, code, type'];
    }

    protected function getData(IdentifiedObject $variable): array
    {
        /** @var ExperimentVariable $variable */
        if($variable != null) {
            return  [
                'id' => $variable->getId(),
                'name' => $variable->getName(),
                'code' => $variable->getCode(),
                'type' => $variable->getType(),
                'values' => $variable->getValues()->map(function (ExperimentValues $value) {
                    return ['id' => $value->getId(), 'experiment' => ['id' => $value->getExperimentId()->getId(), 'name' => $value->getExperimentId()->getName()],  'unit' => ['id' => $value->getUnitId()->getId(), 'name' => $value->getUnitId()->getName()],'time' => $value->getTime(), 'value' => $value->getValue()];
                })->toArray(),
            ];
        }
    }

    protected function setData(IdentifiedObject $variable, ArgumentParser $data): void
    {
        /** @var ExperimentVariable $variable */
        !$data->hasKey('name') ?: $variable->setName($data->getString('name'));
        !$data->hasKey('code') ?: $variable->setCode($data->getString('code'));
        !$data->hasKey('type') ?: $variable->setType($data->getString('type'));
    }

    protected function createObject(ArgumentParser $body): IdentifiedObject
    {
        if (!$body->hasKey('name'))
            throw new MissingRequiredKeyException('name');
        if (!$body->hasKey('code'))
            throw new MissingRequiredKeyException('code');
        if (!$body->hasKey('type'))
            throw new MissingRequiredKeyException('type');
        return new ExperimentVariable();
    }

    protected function checkInsertObject(IdentifiedObject $variable): void
    {
        /** @var ExperimentVariable $variable */
        if ($variable->getName() === null)
            throw new MissingRequiredKeyException('name');
        if ($variable->getCode() === null)
            throw new MissingRequiredKeyException('code');
        if ($variable->getType() === null)
            throw new MissingRequiredKeyException('type');
    }

    public function delete(Request $request, Response $response, ArgumentParser $args): Response
    {
        /** @var ExperimentVariable $variable */
        $variable = $this->getObject($args->getInt('id'));
        if (!$variable->getValues()->isEmpty())
            throw new DependentResourcesBoundException('values');
        return parent::delete($request, $response, $args);
    }

    protected function getValidator(): Assert\Collection
    {
        return new Assert\Collection([
            'name' => new Assert\Type(['type' => 'string']),
            'code' => new Assert\Type(['type' => 'string']),
            'type' => new Assert\Type(['type' => 'string']),
        ]);
    }

    protected static function getObjectName(): string
    {
        return 'variable';
    }

    protected static function getRepositoryClassName(): string
    {
        return ExperimentVariableRepository::Class;
    }
}
