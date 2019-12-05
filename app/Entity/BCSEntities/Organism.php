<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="organism")
 */
class Organism implements IdentifiedObject
{
	use Identifier;

	/**
	 * @var string
	 * @ORM\Column(type="string")
	 */
	protected $name;

	/**
	 * @var string
	 * @ORM\Column(type="string")
	 */
	protected $code;

	/**
	 * @var ArrayCollection
	 * @ORM\OneToMany(targetEntity="Experiment", mappedBy="organismId")
	 */
	private $experiments;

	public function getCode(): ?string
	{
		return $this->code;
	}

	public function setCode(string $code): void
	{
		$this->code = $code;
	}

	/**
	 * @return string
	 */
	public function getName(): ?string
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName(string $name): void
	{
		$this->name = $name;
	}

	/**
	 * @return Experiment[]|Collection
	 */
	public function getExperiment(): Collection
	{
		return $this->experiments;
	}
}
