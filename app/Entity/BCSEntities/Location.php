<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="location")
 * @ORM\DiscriminatorColumn(name="hierarchy_type", type="string")
 */
class Location implements IdentifiedObject
{
	use EBase;

	/**
	 * @var string
	 * @ORM\Column(type="string", name="description")
	 */
	private $description;

	/**
	 * @var string
	 * @ORM\Column(type="string", name="longitude")
	 */
	private $longitude;

    /**
     * @var string
     * @ORM\Column(type="string", name="latitude")
     */
    private $latitude;

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
	 * @param string $description
	 * @return Location
	 */
	public function setDescription($description): Location
	{
		$this->description = $description;
		return $this;
	}

	/**
	 * Get longitude
	 * @return string
	 */
	public function getLongitude(): string
	{
		return $this->longitude;
	}

	/**
	 * Set longitude
	 * @param string $longitude
	 * @return Location
	 */
	public function setLongitude($longitude): Location
	{
		$this->longitude = $longitude;
		return $this;
	}

    /**
     * Get latitude
     * @return string
     */
    public function getLatitude(): string
    {
        return $this->latitude;
    }

    /**
     * Set latitude
     * @param string $latitude
     * @return Location
     */
    public function setLatitude($latitude): Location
    {
        $this->latitude = $latitude;
        return $this;
    }
}
