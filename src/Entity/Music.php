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
 * @Vich\Uploadable
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
     * @ORM\Column(type="string", length=255)
     * @var string|null
     */
    private $musicname;

    /**
     * @Vich\UploadableField(mapping="music_file", fileNameProperty="musicname")
     * @var File|null
     * @Assert\File(
     *     maxSize="100Mi",
     *     mimeTypes={"audio/mpeg", "audio/mp3", "application/octet-stream","application/x-font-gdos","audio/x-wav"},
     * )
     */
    private $musicFile;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTimeInterface|null
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Album::class, inversedBy="musics", cascade={"persist"})
     * @ORM\JoinColumn(onDelete="SET NULL"))
     */
    private $Album;

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

    public function getCreatedDate(): ?\DateTimeInterface
    {
        return $this->createdDate;
    }

    public function setCreatedDate(?\DateTimeInterface $createdDate): self
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    public function getAlbum(): ?Album
    {
        return $this->Album;
    }

    public function setAlbum(?Album $Album): self
    {
        $this->Album = $Album;

        return $this;
    }

    public function setMusicFile(?File $file = null): void
    {
//        $fileName = $file->getClientOriginalName();
        $this->musicFile = $file;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($this->musicFile instanceof UploadedFile) {
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getMusicFile()
    {
        return $this->musicFile;
    }

    public function setMusicName($musicname)
    {
        $this->musicname = $musicname;
    }

    public function getMusicName()
    {
        return $this->musicname;
    }

}
