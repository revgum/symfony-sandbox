<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as Serializer;

/**
 * Site
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\SiteRepository")
 */
class Site
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Groups({"elastica"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Serializer\Groups({"elastica"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     * @Serializer\Groups({"elastica"})
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="Property", mappedBy="site", cascade={"persist"})
     * @Serializer\Groups({"elastica"})
     */
    protected $properties;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Site
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Site
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function __construct()
    {
        $this->properties = new ArrayCollection();
    }

    /**
     * Add property
     *
     * @param \AppBundle\Entity\Property $property
     *
     * @return Site
     */
    public function addProperty(\AppBundle\Entity\Property $property)
    {
        $property->setSite($this);
        $this->properties->add($property);

        return $this;
    }

    /**
     * Remove property
     *
     * @param \AppBundle\Entity\Property $property
     */
    public function removeProperty(\AppBundle\Entity\Property $property)
    {
        $this->properties->removeElement($property);
    }

    /**
     * Get properties
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProperties()
    {
        return $this->properties;
    }
}
