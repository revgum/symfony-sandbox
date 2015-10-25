<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use JMS\Serializer\Annotation as Serializer;

/**
 * Image
 *
 * @ORM\Table()
 * @ORM\Entity
 * @Vich\Uploadable
 */
class Image
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
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"elastica"})
     *
     * @var string
     */
    private $imageName;


    /**
     * @ORM\Column(type="datetime")
     * @Serializer\Groups({"elastica"})
     *
     * @var \DateTime
     */
    private $updatedAt;


    /**
     * @ORM\ManyToOne(targetEntity="Property", inversedBy="images")
     * @ORM\JoinColumn(name="property_id", referencedColumnName="id")
     */
    protected $property;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     * @Vich\UploadableField(mapping="property_image", fileNameProperty="imageName")
     * @Assert\Image(maxSize="1M")
     *
     * @var File
     */
    private $imageFile;

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     */
    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        if ($image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime('now');
        }
    }

    /**
     * @return File
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * @param string $imageName
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;
    }

    /**
     * @return string
     */
    public function getImageName()
    {
        return $this->imageName;
    }

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
     * Set property
     *
     * @param \AppBundle\Entity\Property $property
     *
     * @return Image
     */
    public function setProperty(\AppBundle\Entity\Property $property = null)
    {
        $this->property = $property;

        return $this;
    }

    /**
     * Get property
     *
     * @return \AppBundle\Entity\Property
     */
    public function getProperty()
    {
        return $this->property;
    }
}