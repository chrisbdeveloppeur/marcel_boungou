<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EventRepository::class)
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
     * @ORM\Column(type="string", length=255)
     */
    private $cp;

    /**
     * @ORM\Column(type="string", length=255)
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

    public function __construct()
    {
        $this->datetime = new \DateTime();
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
        date_default_timezone_set("Europe/Paris");
        $this->datetime = $datetime;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

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
                array_splice($array,$key);
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


}
