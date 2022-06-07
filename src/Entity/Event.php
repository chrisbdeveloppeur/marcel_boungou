<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=EventRepository::class)
 * @Vich\Uploadable()
 */
class Event
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datetime;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cp;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $street;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $ics_file;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $mails_to_remind = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $image;

    /**
     * @Vich\UploadableField(mapping="event_img", fileNameProperty="image")
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
    private $ticketing_link;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $tags;

    /**
     * ORM\Column(type="array", nullable=true)
     */
    private $array_tags;


    public function __construct()
    {
        $this->datetime = new \DateTime();
//        $tagFormatted = strtolower(preg_replace('~[\\\\/:*?"<>|()&, \']~','',$tag));
        $this->tags = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getTitle();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        $this->setIcsFile($title.'.ics');

        return $this;
    }

    public function getDatetime(): ?\DateTime
    {
        return $this->datetime;
    }

    public function setDatetime(\DateTime $datetime): self
    {
//        date_default_timezone_set("Europe/Paris");
        $this->datetime = $datetime;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = strtoupper($country);

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCp(): ?string
    {
        return $this->cp;
    }

    public function setCp(string $cp): self
    {
        $this->cp = $cp;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getIcsFile(): ?string
    {
        return $this->ics_file;
    }

    public function setIcsFile(string $ics_file): self
    {
        $this->ics_file = $ics_file;
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getMailsToRemind(): ?array
    {
        return $this->mails_to_remind;
    }

    public function addMailToRemind(?string $mail)
    {
        $array = $this->mails_to_remind;
        if (!in_array($mail, $array)){
            array_push($array,$mail);
            $this->mails_to_remind = $array;
        }
        return $this;
    }


    /**
     * @param string|null $mail
     * @return $this
     */
    public function removeMailToRemind(?string $mail)
    {
        $array = $this->mails_to_remind;
        if (in_array($mail, $array)){
            if (($key = array_search($mail, $array, true)) !== false) {
                unset($array[$key]);
            }
            $this->mails_to_remind = $array;
        }
        return $this;
    }

    public function getAdresse()
    {
        $adresse = $this->street.', '.$this->cp.' '.$this->city.', '.$this->country;
        return $adresse;
    }

    public function getTicketingLink(): ?string
    {
        return $this->ticketing_link;
    }

    public function setTicketingLink(?string $ticketing_link): self
    {
        $this->ticketing_link = $ticketing_link;

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

}
