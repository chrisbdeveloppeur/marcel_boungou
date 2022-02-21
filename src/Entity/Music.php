<?php

namespace App\Entity;

use App\Repository\MusicRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=MusicRepository::class)
 */
class Music
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
    private $titre;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duration;

    /**
     *
     * @Vich\UploadableField(mapping="music_file", fileNameProperty="musicName")
     *
     * @var File|null
     * @Assert\File(
     *     maxSize="100Mi",
     *     mimeTypes={"audio/mpeg","application/octet-stream","application/x-font-gdos","audio/x-wav"})
     */
    private $musicFile;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null
     */
    private $musicName;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $createdDate;

    public function __construct()
    {
        $this->createdDate = new \DateTime('now');
    }

    public function __toString()
    {
        return $this->titre;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return File|null
     */
    public function getMusicFile(): ?File
    {
        return $this->musicFile;
    }

    /**
     * @param File|null $musicFile
     * @return Music
     */
    public function setMusicFile(?File $musicFile): Music
    {
        $this->musicFile = $musicFile;
        if ($this->musicFile instanceof UploadedFile) {
            $this->updatedAt = new \DateTime('now');
        }
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMusicName(): ?string
    {
        return $this->musicFile;
    }

    /**
     * @param string|null $musicName
     * @return Music
     */
    public function setMusicName(?string $musicName): Music
    {
        $this->musicName = $musicName;
        return $this;
    }

    public function getCreatedDate(): ?\DateTimeInterface
    {
        return $this->createdDate;
    }

    public function setCreatedDate(?\DateTimeInterface $createdDate): self
    {
        $this->createdDate = $createdDate;

        return $this;
    }
}
