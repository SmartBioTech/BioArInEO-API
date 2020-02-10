<?php

namespace App\Entity;

use App\Helpers\DateTimeJson;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="experiment_to_device_measure")
 * @ORM\DiscriminatorColumn(name="hierarchy_type", type="string")
 */
class ExperimentDeviceMeasure implements IdentifiedObject
{
	use EBase;

    /**
     * @ORM\ManyToOne(targetEntity="Experiment", inversedBy="devices")
     * @ORM\JoinColumn(name="exp_id", referencedColumnName="id")
     */
    protected $experimentId;

    /**
     * @ORM\ManyToOne(targetEntity="Device")
     * @ORM\JoinColumn(name="dev_id", referencedColumnName="id")
     */
    protected $deviceId;

    /**
     * @ORM\ManyToOne(targetEntity="Location")
     * @ORM\JoinColumn(name="loc_id", referencedColumnName="id")
     */
    protected $locationId;

    /**
     * @var DateTimeJson
     * @ORM\Column(type="datetime", name="time")
     */
    private $time;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="DeviceMeasureValue", mappedBy="measureId")
     */
    private $values;

	/**
	 * Get type
	 * @return Experiment/null
	 */
	public function getExperimentId(): ?Experiment
	{
		return $this->experimentId;
	}

	/**
	 * Set type
	 * @param integer $experimentId
	 * @return ExperimentDeviceMeasure
	 */
	public function setExperimentId($experimentId): ExperimentDeviceMeasure
	{
		$this->experimentId = $experimentId;
		return $this;
	}

	/**
	 * Get deviceId
	 * @return Device
	 */
	public function getDeviceId(): Device
	{
		return $this->deviceId;
	}

	/**
	 * Set deviceId
	 * @param integer $deviceId
	 * @return ExperimentDeviceMeasure
	 */
	public function setDeviceId($deviceId): ExperimentDeviceMeasure
	{
		$this->deviceId = $deviceId;
		return $this;
	}

	/**
	 * Get locationId
	 * @return Location
	 */
	public function getLocationId(): Location
	{
		return $this->locationId;
	}

	/**
	 * Set locationId
	 * @param int $locationId
	 * @return ExperimentDeviceMeasure
	 */
	public function setLocationId($locationId): ExperimentDeviceMeasure
	{
		$this->locationId = $locationId;
		return $this;
	}

    /**
     * Get time
     * @return DateTimeJson
     */
    public function getTime(): DateTimeJson
    {
        return $this->time;
    }

    /**
     * Set time
     * @param string $time
     * @return ExperimentDeviceMeasure
     */
    public function setTime(string $time): ExperimentDeviceMeasure
    {
        $this->time = date_create_from_format('d/m/Y:H:i:s', $time);
        return $this;
    }

    /**
     * @return DeviceMeasureValue[]|Collection
     */
    public function getValues(): Collection
    {
        return $this->values;
    }
}
