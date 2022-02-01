<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/event", name="events_")
 */
class EventsController extends AbstractController
{

    private $calendarController;

    public function __construct(CalendarController $calendarController)
    {
        $this->calendarController = $calendarController;
    }

    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findAll();
        return $this->render('includes/calendar.html.twig', [
            'events' => $events,
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN')", statusCode=403, message="Access denied !")
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($event);
            $entityManager->flush();
            $this->calendarController->createIcsFile($event->getId());

            return $this->redirectToRoute('events_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event/new.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')", statusCode=403, message="Access denied !")
     */
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN')", statusCode=403, message="Access denied !")
     */
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('events_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"POST"})
     * @Security("is_granted('ROLE_ADMIN')", statusCode=403, message="Access denied !")
     */
    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('events_index', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * @Route("/reminder/{id}", name="add_email_reminder", methods={"POST","GET"})
     */
    public function addEmailReminder($id, EventRepository $eventRepository){
        $event = $eventRepository->find($id);

        if ($_POST){
            $mail = $_POST['email'];
            $event->addMailToRemind($mail);
            return $this->redirectToRoute('');
        }
    }

}
