<?php

namespace App\Entity;

use App\Repository\PictureRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PictureRepository::class)
 * @Vich\Uploadable()
 */
class Picture
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $image;

    /**
     * @Vich\UploadableField(mapping="picture_img", fileNameProperty="image")
     * @var File
     * @Assert\File(
     *     mimeTypes={"image/jpeg", "image/jpg","image/bmp", "image/png", "image/svg"},
     *     mimeTypesMessage="The type of the file is invalid ({{ type }})",
     * )
     */
    private $imageFile;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $tags;

    /**
     * ORM\Column(type="array", nullable=true)
     */
    private $array_tags;

    public function __toString()
    {
        if ($this->title){
            return $this->title;
        }else{
            return $this->image;
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($image) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getImageFile()
    {
        return $this->imageFile;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function getTagsInArray()
    {
        $this->array_tags = explode(",",$this->tags);
        return $this->array_tags;
    }

    public function setTags($tags)
    {
        $tags = strtolower(preg_replace('~[\\\\/:*?"<>|() &\']~','',$tags));
        $this->tags = $tags;

        return $this;
    }
}
