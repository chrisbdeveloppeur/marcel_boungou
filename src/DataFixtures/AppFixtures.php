<?php

namespace App\DataFixtures;

use App\Controller\CalendarController;
use App\Entity\Event;
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

        for ($e = 1; $e <= 10; $e++){
            $event = new Event();
            $event->setDatetime($faker->dateTime());
            $event->setTitle($faker->word);
            $event->setCountry($faker->country);
            $event->setCity($faker->city);
            $event->setCp($faker->postcode);
            $event->setStreet($faker->streetAddress);
            $manager->persist($event);
            $manager->flush();
            $this->calendar->createIcsFile($event->getId());
        }

    }
}
