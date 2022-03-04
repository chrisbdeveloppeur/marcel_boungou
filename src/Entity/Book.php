<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=BookRepository::class)
 * @Vich\Uploadable()
 */
class Book
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $redirect_link;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $tags;

    /**
     * ORM\Column(type="array", nullable=true)
     */
    private $array_tags;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $image;

    /**
     * @Vich\UploadableField(mapping="book_img", fileNameProperty="image")
     * @var File
     * @Assert\File(
     *     mimeTypes={"image/jpeg", "image/jpg","image/bmp", "image/png", "image/svg"},
     *     mimeTypesMessage="The type of the file is invalid ({{ type }})",
     * )
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $imageVerso;

    /**
     * @Vich\UploadableField(mapping="book_img", fileNameProperty="imageVerso")
     * @var File
     * @Assert\File(
     *     mimeTypes={"image/jpeg", "image/jpg","image/bmp", "image/png", "image/svg"},
     *     mimeTypesMessage="The type of the file is invalid ({{ type }})",
     * )
     */
    private $imageVersoFile;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $updatedAt;

    public function __toString()
    {
        return $this->title;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getRedirectLink(): ?string
    {
        return $this->redirect_link;
    }

    public function setRedirectLink(?string $redirect_link): self
    {
        $this->redirect_link = $redirect_link;

        return $this;
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

    public function getUpdatedAt(){
        return $this->updatedAt;
    }

    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;
        if ($image) {
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

    public function setImageVersoFile(File $imageVerso = null)
    {
        $this->imageVersoFile = $imageVerso;
        if ($imageVerso) {
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getImageVersoFile()
    {
        return $this->imageVersoFile;
    }

    public function setImageVerso($imageVerso)
    {
        $this->imageVerso = $imageVerso;
    }

    public function getImageVerso()
    {
        return $this->imageVerso;
    }
}
