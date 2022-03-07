<?php

namespace App\EventSubscriber;

use App\Repository\AlbumRepository;
use App\Repository\BookRepository;
use App\Repository\EventRepository;
use App\Repository\MusicRepository;
use App\Repository\PictureRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment;

class TwigEventSubscriber implements EventSubscriberInterface
{
    private $twig, $eventRepository, $musicRepository, $albumRepository, $bookRepository, $pictureRepository;


    public function __construct(Environment $twig, EventRepository $eventRepository, MusicRepository $musicRepository, AlbumRepository $albumRepository, BookRepository $bookRepository, PictureRepository $pictureRepository)
    {
        $this->twig = $twig;
        $this->eventRepository = $eventRepository;
        $this->musicRepository = $musicRepository;
        $this->albumRepository = $albumRepository;
        $this->bookRepository = $bookRepository;
        $this->pictureRepository = $pictureRepository;
    }

    public function onControllerEvent(ControllerEvent $event)
    {
        $date = new \DateTime('now');
        $events = $this->eventRepository->findByDate($date);
        $musics = $this->musicRepository->findAll();
        $albums = $this->albumRepository->findByYear();
        $books = $this->bookRepository->findAll();
        $pictures = $this->pictureRepository->findAll();
        $this->twig->addGlobal('events', $events);
        $this->twig->addGlobal('musics', $musics);
        $this->twig->addGlobal('albums', $albums);
        $this->twig->addGlobal('books', $books);
        $this->twig->addGlobal('pictures', $pictures);
    }

    public static function getSubscribedEvents()
    {
        return [
            ControllerEvent::class => 'onControllerEvent',
        ];
    }
}
