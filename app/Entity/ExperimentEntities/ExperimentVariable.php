<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="variable")
 * @ORM\DiscriminatorColumn(name="hierarchy_type", type="string")
 */
class ExperimentVariable implements IdentifiedObject
{
	use EBase;

	/**
	 * @var string
	 * @ORM\Column(type="string", name="name")
	 */
	private $name;

	/**
	 * @var string
	 * @ORM\Column(type="string", name="code")
	 */
	private $code;

	/**
	 * @var string
     * @ORM\Column(type="string", columnDefinition="ENUM('measured','computed','adjusted','aggregate')")
	 */
	private $type;

	/**
	 * @var ArrayCollection
	 * @ORM\OneToMany(targetEntity="ExperimentValues", mappedBy="variableId")
	 */
	protected $values;

	/**
	 * @var ArrayCollection
	 * @ORM\OneToMany(targetEntity="BioquantityVariable", mappedBy="experimentVariableId")
	 */
	protected $bioquantityVariables;

	/**
	 * @var ArrayCollection
	 * @ORM\OneToMany(targetEntity="ExperimentNote", mappedBy="variableId")
	 */
	private $notes;

	/**
	 * Get id
	 * @return integer
	 */
	public function getId(): int
	{
		return $this->id;
	}


	/**
	 * Get name
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * Set name
	 * @param string $name
	 * @return Experiment
	 */
	public function setName($name): ExperimentVariable
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * Get code
	 * @return string
	 */
	public function getCode(): ?string
	{
		return $this->code;
	}

	/**
	 * Set code
	 * @param string $code
	 * @return ExperimentVariable
	 */
	public function setCode($code): ExperimentVariable
	{
		$this->code = $code;
		return $this;
	}

	/**
	 * Get type
	 * @return string
	 */
	public function getType(): ?string
	{
		return $this->type;
	}

	/**
	 * Set type
	 * @param string $type
	 * @return ExperimentVariable
	 */
	public function setType($type): ExperimentVariable
	{
        if (!in_array($type, array('measured','computed','adjusted','aggregate'))) {
            throw new \InvalidArgumentException("Invalid type");
        }
        $this->type = $type;
        return $this;
	}

	/**
	 * @return ExperimentValues[]|Collection
	 */
	public function getValues(): Collection
	{
		return $this->values;
	}

	/**
	 * @return ExperimentNote[]|Collection
	 */
	public function getNote(): Collection
	{
		return $this->notes;
	}

    /**
     * @return BioquantityVariable[]|Collection
     */
    public function getBioquantities(): Collection
    {
        return $this->bioquantityVariables;
    }
}