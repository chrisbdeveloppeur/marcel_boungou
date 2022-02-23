<?php

namespace App\EventSubscriber;

use App\Repository\AlbumRepository;
use App\Repository\EventRepository;
use App\Repository\MusicRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment;

class TwigEventSubscriber implements EventSubscriberInterface
{
    private $twig;
    private $eventRepository;
    private $musicRepository;
    private $albumRepository;

    public function __construct(Environment $twig, EventRepository $eventRepository, MusicRepository $musicRepository, AlbumRepository $albumRepository)
    {
        $this->twig = $twig;
        $this->eventRepository = $eventRepository;
        $this->musicRepository = $musicRepository;
        $this->albumRepository = $albumRepository;
    }

    public function onControllerEvent(ControllerEvent $event)
    {
        $date = new \DateTime('now');
        $events = $this->eventRepository->findByDate($date);
        $musics = $this->musicRepository->findAll();
        $albums = $this->albumRepository->findByYear();
        $this->twig->addGlobal('events', $events);
        $this->twig->addGlobal('musics', $musics);
        $this->twig->addGlobal('albums', $albums);
    }

    public static function getSubscribedEvents()
    {
        return [
            ControllerEvent::class => 'onControllerEvent',
        ];
    }
}
