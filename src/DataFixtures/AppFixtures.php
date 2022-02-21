<?php

namespace App\DataFixtures;

use App\Controller\CalendarController;
use App\Entity\Event;
use App\Entity\Music;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AppFixtures extends Fixture
{
    private $calendar;
    public function __construct(CalendarController $calendarController)
    {
        $this->calendar = $calendarController;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        date_default_timezone_set('Europe/Paris');
        $date = new \DateTime('yesterday');
        for ($e = -1; $e <= 10; $e++){
            $event = new Event();
            $time = $faker->time();
            $event->setDatetime($date->modify('+'.$e.' day ' . $time));
            $event->setTitle($faker->word);
            $event->setCountry($faker->country);
            $event->setCity($faker->city);
            $event->setCp($faker->postcode);
            $event->setStreet($faker->streetAddress);
            $event->setImage('https://picsum.photos/id/'.$faker->numberBetween(0,1000).'/720/240');
            $event->setDescription($faker->text($faker->numberBetween('100','500')));
            $event->setTicketingLink('https://www.fnac.com/ia233457/Marcel-Boungou');
            $event->addTag($faker->word);
            for ($j = 1; $j <= $faker->numberBetween('2','10'); $j++){
                $mail = $faker->email;
                $event->addMailToRemind($mail);
            }
            $manager->persist($event);
            $manager->flush();
            $this->calendar->createIcsFile($event->getId());
        }

        for ($e = -1; $e <= 10; $e++){
            $music = new Music();
            $music->setTitre($faker->word);
            $music->setCreatedDate(new \DateTime('now'));
            $music->setMusicName('zola-ye-yenge.mp3');
            $manager->persist($music);
            $manager->flush();
        }

    }
}
