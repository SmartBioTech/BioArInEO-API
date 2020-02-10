<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="device_measure_value")
 * @ORM\DiscriminatorColumn(name="hierarchy_type", type="string")
 */
class DeviceMeasureValue implements IdentifiedObject
{
	use EBase;

    /**
     * @ORM\ManyToOne(targetEntity="ExperimentDeviceMeasure")
     * @ORM\JoinColumn(name="msr_id", referencedColumnName="id")
     */
    protected $measureId;

	/**
	 * @ORM\ManyToOne(targetEntity="ExperimentVariable")
	 * @ORM\JoinColumn(name="var_id", referencedColumnName="id")
	 */
	protected $variableId;

    /**
     * @ORM\ManyToOne(targetEntity="Unit")
     * @ORM\JoinColumn(name="unit_id", referencedColumnName="id")
     */
    protected $unitId;

	/**
	 * @var float
	 * @ORM\Column(type="float", name="value")
	 */
	private $value;
	
	/**
	 * Get id
	 * @return integer
	 */
	public function getId(): ?int
	{
		return $this->id;
	}

    /**
     * Get measureId
     * @return ExperimentDeviceMeasure/null
     */
    public function getMeasureId(): ?ExperimentDeviceMeasure
    {
        return $this->measureId;
    }

    /**
     * Set measureId
     * @param integer $measureId
     * @return DeviceMeasureValue
     */
    public function setMeasureId($measureId): DeviceMeasureValue
    {
        $this->measureId = $measureId;
        return $this;
    }

    /**
     * Get unitId
     * @return Unit/null
     */
    public function getUnitId(): ?Unit
    {
        return $this->unitId;
    }

    /**
     * Set unitId
     * @param integer $unitId
     * @return DeviceMeasureValue
     */
    public function setUnitId($unitId): DeviceMeasureValue
    {
        $this->unitId = $unitId;
        return $this;
    }

	/**
	 * Get variableId
	 * @return ExperimentVariable
	 */
	public function getVariableId(): ?ExperimentVariable
	{
		return $this->variableId;
	}

	/**
	 * Set variableId
	 * @param integer $variableId
	 * @return DeviceMeasureValue
	 */
	public function setVariableId($variableId): DeviceMeasureValue
	{
		$this->variableId = $variableId;
		return $this;
	}

	/**
	 * Get value
	 * @return float
	 */
	public function getValue(): float
	{
		return $this->value;
	}

	/**
	 * Set value
	 * @param float $value
	 * @return DeviceMeasureValue
	 */
	public function setValue($value): DeviceMeasureValue
	{
		$this->value = $value;
		return $this;
	}
}