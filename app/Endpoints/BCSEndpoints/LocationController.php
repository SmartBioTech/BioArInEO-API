<?php

namespace App\Controllers;

use App\Entity\{IdentifiedObject,
    Location,
    Repositories\IEndpointRepository,
    Repositories\LocationRepository,
    Repositories\UnitRepository,
    Unit};
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
 * @method Location getObject(int $id, IEndpointRepository $repository = null, string $objectName = null)
 */
final class LocationController extends WritableRepositoryController
{
    /** @var LocationRepository */
    private $locationRepository;

    public function __construct(Container $c)
    {
        parent::__construct($c);
        $this->locationRepository = $c->get(LocationRepository::class);
    }

    protected static function getAllowedSort(): array
    {
        return ['id, description, longitude, latitude'];
    }

    protected function getData(IdentifiedObject $location): array
    {
        /** @var Location $location */
        if($location != null) {
            return  [
                'id' => $location->getId(),
                'description' => $location->getDescription(),
                'longitude' => $location->getLongitude(),
                'latitude' => $location->getLatitude()];
        }
    }

    protected function setData(IdentifiedObject $location, ArgumentParser $data): void
    {
        /** @var Location $location */
        !$data->hasKey('description') ?: $location->setDescription($data->getString('description'));
        !$data->hasKey('longitude') ?: $location->setLongitude($data->getString('longitude'));
        !$data->hasKey('latitude') ?: $location->setLatitude($data->getString('latitude'));
    }

    protected function createObject(ArgumentParser $body): IdentifiedObject
    {
        if (!$body->hasKey('longitude'))
            throw new MissingRequiredKeyException('longitude');
        if (!$body->hasKey('latitude'))
            throw new MissingRequiredKeyException('latitude');
        return new Location();
    }

    protected function checkInsertObject(IdentifiedObject $location): void
    {
        /** @var Location $location */
        if ($location->getLongitude() === null)
            throw new MissingRequiredKeyException('longitude');
        if ($location->getLatitude() === null)
            throw new MissingRequiredKeyException('latitude');
    }

    public function delete(Request $request, Response $response, ArgumentParser $args): Response
    {
        return parent::delete($request, $response, $args);
    }

    protected function getValidator(): Assert\Collection
    {
        return new Assert\Collection([
            'longitude' => new Assert\Type(['type' => 'string']),
            'latitude' => new Assert\Type(['type' => 'string']),
        ]);
    }

    protected static function getObjectName(): string
    {
        return 'location';
    }

    protected static function getRepositoryClassName(): string
    {
        return LocationRepository::Class;
    }
}