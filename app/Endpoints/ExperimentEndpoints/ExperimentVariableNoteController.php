<?php

namespace App\Controllers;

use App\Entity\{ExperimentNote,
    ExperimentVariable,
    IdentifiedObject,
    Repositories\ExperimentVariableNoteRepository,
    Repositories\ExperimentVariableRepository,
    Repositories\IEndpointRepository,
    Repositories\ExperimentRepository,
    Repositories\ExperimentNoteRepository,
    Repositories\VariableNoteRepository};
use App\Exceptions\
{
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
 * @property-read ExperimentNoteRepository $repository
 * @method ExperimentNote getObject(int $id, IEndpointRepository $repository = null, string $objectName = null)
 */
final class ExperimentVariableNoteController extends ParentedRepositoryController
{
	/** @var VariableNoteRepository */
	private $noteRepository;

	public function __construct(Container $v)
	{
		parent::__construct($v);
		$this->noteRepository = $v->get(ExperimentVariableNoteRepository::class);
	}

	protected static function getAllowedSort(): array
	{
		return ['id', 'time', 'note'];
	}

	protected function getData(IdentifiedObject $note): array
	{
		/** @var ExperimentNote $note */
		return [
			'time' => $note->getTime(),
			'note' => $note->getNote(),
			'imgLink' => $note->getImgLink(),
		];
	}

	protected function setData(IdentifiedObject $note, ArgumentParser $data): void
	{
		/** @var ExperimentNote $note */
		$note->getVariableId() ?: $note->setVariableId($this->repository->getParent());
		!$data->hasKey('time') ?: $note->setTime($data->getFloat('time'));
		!$data->hasKey('note') ?: $note->setNote($data->getString('note'));
		!$data->hasKey('imgLink') ?: $note->setImgLink($data->getString('imgLink'));
	}

	protected function createObject(ArgumentParser $body): IdentifiedObject
	{
		return new ExperimentNote;
	}

	protected function checkInsertObject(IdentifiedObject $note): void
	{
		/** @var ExperimentNote $note */
		if ($note->getVariableId() === null)
			throw new MissingRequiredKeyException('experimentId');
	}

	public function delete(Request $request, Response $response, ArgumentParser $args): Response
	{
		/** @var ExperimentNote $note */
		$note = $this->getObject($args->getInt('id'));
		return parent::delete($request, $response, $args);
	}

	protected function getValidator(): Assert\Collection
	{
		return new Assert\Collection( [
			'experimentVariableId' => new Assert\Type(['type' => 'integer']),
		]);
	}

	protected static function getObjectName(): string
	{
		return 'experimentNote';
	}

	protected static function getRepositoryClassName(): string
	{
		return ExperimentVariableNoteRepository::Class;
	}

	protected static function getParentRepositoryClassName(): string
	{
		return ExperimentVariableRepository::class;
	}

	protected function getParentObjectInfo(): array
	{
		return ['variable-id', 'variable'];
	}
}
