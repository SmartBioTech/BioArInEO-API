<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use App\Helpers\DateTimeJson;

/**
 * @ORM\Entity
 * @ORM\Table(name="experiment")
 * @ORM\DiscriminatorColumn(name="hierarchy_type", type="string")
 */
class Experiment implements IdentifiedObject
{

    const PRIVACY_PUBLIC = 'public';
    const PRIVACY_PRIVATE = 'private';
    const STATUS_RUNNING = 'running';
    const STATUS_FINISHED = 'finished';
    const STATUS_FAILED = 'failed';

	use EBase;
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

	/**
	 * @var string
	 * @ORM\Column(type="string", name="name")
	 */
	private $name;

	/**
	 * @var string
	 * @ORM\Column(type="string", name="description")
	 */
	private $description;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\Organism", inversedBy="experiments", fetch="EAGER")
	 * @ORM\JoinColumn(name="organism_id", referencedColumnName="id")
	 */
	private $organismId;

	/**
	 * @var string
	 * @ORM\Column(type="string", name="protocol")
	 */
	//private $protocol;

	/**
	 * @var DateTimeJson
	 * @ORM\Column(type="datetime", name="started")
	 */
	private $started;

	/**
	 * @var DateTimeJson
	 * @ORM\Column(type="datetime", name="inserted")
	 */
	private $inserted;

	/**User neexistuje
	 * @ORM\ManyToOne(targetEntity="...", inversedBy="...")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
	//private $userId;

    /**
     * @var string
     * @ORM\Column(type="string", columnDefinition="ENUM('private', 'public')")
     */
    private $privacy;

	/**
	 * @var string
     * @ORM\Column(type="string", columnDefinition="ENUM('finished', 'running', 'failed')")
	 */
	private $status;

	/**
	 * @var ArrayCollection
	 * @ORM\OneToMany(targetEntity="ExperimentValues", mappedBy="experimentId")
	 */
	private $values;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="ExperimentDeviceMeasure", mappedBy="experimentId")
     */
    private $devices;

	/**
	 * @var ArrayCollection
	 * @ORM\OneToMany(targetEntity="ExperimentNote", mappedBy="experimentId")
	 */
	private $notes;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="ExperimentEvent", mappedBy="experimentId")
     */
    private $events;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Experiment", inversedBy="experimentRelation")
     * @ORM\JoinTable(name="experiment_to_experiment", joinColumns={@ORM\JoinColumn(name="1exp_id", referencedColumnName="id")},
     * inverseJoinColumns={@ORM\JoinColumn(name="2exp_id", referencedColumnName="id")})
     */
    private $experimentRelation;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Experiment", inversedBy="experimentParent")
     * @ORM\JoinTable(name="experiment_parented", joinColumns={@ORM\JoinColumn(name="parented_exp_id", referencedColumnName="id")},
     * inverseJoinColumns={@ORM\JoinColumn(name="child_exp_id", referencedColumnName="id")})
     */
    private $experimentChildren;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Protocol", inversedBy="experiments")
     * @ORM\JoinTable(name="experiment_to_protocol", joinColumns={@ORM\JoinColumn(name="exp_id", referencedColumnName="id")},
     * inverseJoinColumns={@ORM\JoinColumn(name="protocol_id", referencedColumnName="id")})
     */
    private $protocols;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Bioquantity", inversedBy="experiments")
     * @ORM\JoinTable(name="bioquantity_to_experiment", joinColumns={@ORM\JoinColumn(name="exp_id", referencedColumnName="id")},
     * inverseJoinColumns={@ORM\JoinColumn(name="bionum_id", referencedColumnName="id")})
     */
    private $bioquantities;


    public function __construct()
    {
        $this->inserted = new DateTimeJson;
        //$this->devices = new ArrayCollection();
        $this->protocols = new ArrayCollection();
        $this->bioquantities = new ArrayCollection();
    }

	/**
	 * Get name
	 * @return string
	 */
	public function getName(): ?string
	{
		return $this->name;
	}

	/**
	 * Set name
	 * @param string $name
	 * @return Experiment
	 */
	public function setName($name): Experiment
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * Get description
	 * @return string
	 */
	public function getDescription(): ?string
	{
		return $this->description;
	}

	/**
	 * Set description
	 * @param integer $description
	 * @return Experiment
	 */
	public function setDescription($description): Experiment
	{
		$this->description = $description;
		return $this;
	}

	/**
	 * Set organismId
	 * @param Organism $organismId
	 * @return Experiment
	 */
	public function setOrganismId($organismId): Experiment
	{
		$this->organismId = $organismId;
		return $this;
	}

	/**
	 * Get organismId
	 * @return Organism
	 */
	public function getOrganismId(): ?Organism
	{
		return $this->organismId;
	}

	/**
	 * Get started
	 * @return DateTimeJson
	 */
	public function getStarted(): DateTimeJson
	{
		return $this->started;
	}

    /**
     * Set started
     * @param string $started
     * @return Experiment
     */
	public function setStarted(string $started): Experiment
	{
		$this->started = date_create_from_format('d/m/Y:H:i:s', $started);
		return $this;
	}

	/**
	 * Get inserted
	 * @return DateTimeJson|null
	 */
	public function getInserted(): ?DateTimeJson
	{
		return $this->inserted;
	}


    /**
     * @param Bioquantity $bioquantity
     */
    public function addBioquantity(Bioquantity $bioquantity)
    {
        if ($this->bioquantities->contains($bioquantity)) {
            return;
        }
        $this->bioquantities->add($bioquantity);
        $bioquantity->addExperiment($this);
    }

    /**
     * @param Bioquantity $bioquantity
     */
    public function removeBioquantity(Bioquantity $bioquantity)
    {
        if (!$this->bioquantities->contains($bioquantity)) {
            return;
        }
        $this->bioquantities->removeElement($bioquantity);
        $bioquantity->removeExperiment($this);
    }

    /**
     * @param  Experiment $experiment
     * @return void
     */
    public function addExperiment(Experiment $experiment)
    {
        if (!$this->experimentRelation->contains($experiment)) {
            $this->experimentRelation->add($experiment);
            $experiment->addExperiment($this);
        }
    }

    /**
     * @param  Experiment $experiment
     * @return void
     */
    public function removeExperiment(Experiment $experiment)
    {
        if ($this->experimentRelation->contains($experiment)) {
            $this->experimentRelation->removeElement($experiment);
            $experiment->removeExperiment($this);
        }
    }

    /**
     * @param  Experiment $child
     * @return void
     */
    public function addChild(Experiment $child)
    {
        if (!$this->experimentChildren->contains($child)) {
            $this->experimentChildren->add($child);
        }
    }

    /**
     * @param  Experiment $child
     * @return void
     */
    public function removeChild(Experiment $child)
    {
        if ($this->experimentChildren->contains($child)) {
            $this->experimentChildren->removeElement($child);
        }
    }

    /**
     * @param  Protocol $protocol
     * @return void
     */
    public function addProtocol(Protocol $protocol)
    {
        if (!$this->protocols->contains($protocol)) {
            $this->protocols->add($protocol);
            $protocol->addExperiment($this);
        }
    }

    /**
     * @param  Protocol $protocol
     * @return void
     */
    public function removeProtocol(Protocol $protocol)
    {
        if ($this->protocols->contains($protocol)) {
            $this->protocols->removeElement($protocol);
            $protocol->removeExperiment($this);
        }
    }

	/**
	 * Get userId
	 * @return integer
	 */
	public function getUserId(): ?int
	{
		return $this->userId;
	}

	/**
	 * Set userId
	 * @param int $userId
	 * @return Experiment
	 */
	public function setUserId($userId): Experiment
	{
		$this->userId = $userId;
		return $this;
	}

    /**
     * Get status
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Set status
     * @param string $status
     * @return Experiment
     */
    public function setStatus($status): Experiment
    {
        if (!in_array($status, array(self::STATUS_FINISHED, self::STATUS_RUNNING, self::STATUS_FAILED))) {
            throw new \InvalidArgumentException("Invalid status");
        }
        $this->status = $status;
        return $this;
    }

	/**
	 * Get privacy
	 * @return string
	 */
	public function getPrivacy(): string
	{
		return $this->privacy;
	}

	/**
	 * Set privacy
	 * @param string $privacy
	 * @return Experiment
	 */
	public function setPrivacy($privacy): Experiment
	{
        if (!in_array($privacy, array(self::PRIVACY_PUBLIC, self::PRIVACY_PRIVATE))) {
            throw new \InvalidArgumentException("Invalid privacy");
        }
        $this->privacy = $privacy;
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
     * @return ExperimentDeviceMeasure[]|Collection
     */
    public function getDevicesMeasures(): Collection
    {
        return $this->devices;
    }

    /**
     * @return Bioquantity[]|Collection
     */
    public function getBioquantities(): Collection
    {
        return $this->bioquantities;
    }

    /**
     * @return Protocol|null
     */
    public function getProtocol(): ?Protocol
    {
        $len = count($this->protocols);
        if($len > 0){
            return $this->protocols[$len - 1];
        }
       return null;
    }

	/**
	 * @return ExperimentNote[]|Collection
	 */
	public function getNote(): Collection
	{
		return $this->notes;
	}

    /**
     * @return ExperimentEvent[]|Collection
     */
    public function getEvent(): Collection
    {
        return $this->events;
    }

    /**
     * @return Experiment[]|Collection
     */
    public function getExperimentRelation(): Collection
    {
        return $this->experimentRelation;
    }

    /**
     * @return Experiment[]|Collection
     */
    public function getChildren(): Collection
    {
        return $this->experimentChildren;
    }
}