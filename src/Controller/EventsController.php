<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Form\RemoveEmailReminderType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/event", name="events_")
 */
class EventsController extends AbstractController
{

    private $calendarController;
    private $translator;

    public function __construct(CalendarController $calendarController, TranslatorInterface $translator)
    {
        $this->calendarController = $calendarController;
        $this->translator = $translator;
    }

    /**
     * @Route("/", name="home", methods={"GET"})
     */
    public function home(): Response
    {
        return $this->render('event/home.html.twig', [
        ]);
    }

    /**
     * @Route("/all", name="all", methods={"GET"})
     */
    public function all(): Response
    {
        return $this->render('event/_all.html.twig', [
        ]);
    }

    /**
     * @Route("/list", name="list", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function list(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findAll();
        return $this->render('event/index.html.twig', [
            'events' => $events,
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN')")
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
            $msg = $this->translator->trans('Event <b>'.$event->getTitle().'</b> created with success');
            $this->addFlash('success',$msg);
            return $this->redirectToRoute('events_home', [], Response::HTTP_SEE_OTHER);
        }

        $redirect_link = $this->redirectToRoute('events_home')->getTargetUrl();
        return $this->render('event/new.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
            'redirect' => [
                'link' => $redirect_link,
                'txt' => $this->translator->trans('Back to Events home'),
            ]
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('info', $this->translator->trans('Changes made successfully'));
            $previousUrl = $request->headers->get('referer');
            return $this->redirect($previousUrl);
            //return $this->redirectToRoute('events_home', [], Response::HTTP_SEE_OTHER);
        }

        $redirect_link = $this->redirectToRoute('events_home')->getTargetUrl();
        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
            'redirect' => [
                'link' => $redirect_link,
                'txt' => $this->translator->trans('Back to Events home'),
            ]
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('events_home', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * @Route("/reminder/add/{id}", name="add_email_reminder", methods={"POST","GET"})
     */
    public function addEmailReminder($id, EventRepository $eventRepository, TranslatorInterface $translator, EntityManagerInterface $em){
        $event = $eventRepository->find($id);

        if ($_POST){
            $mail = $_POST['email'];
            if (in_array($mail, $event->getMailsToRemind())){
                $msg = $translator->trans('You are already in the list of contacts to call back');
                $this->addFlash('warning', $msg);
            }else{
                $msg = $translator->trans('Thanks you ! You will be notified by mail when the event date is approaching');
                $event->addMailToRemind($mail);
                $em->persist($event);
                $em->flush();
                $this->addFlash('success', $msg);
            }

            return $this->redirectToRoute('events_home');
        }
    }


    /**
     * @Route("/reminder/remove/{id}", name="remove_email_reminder", methods={"POST","GET"})
     */
    public function removeEmailReminder($id, EventRepository $eventRepository, TranslatorInterface $translator, EntityManagerInterface $em, Request $request){
        $form = $this->createForm(RemoveEmailReminderType::class);
        $form->handleRequest($request);
        $event = $eventRepository->find($id);

        if ($form->isSubmitted() && $form->isValid()){
            $mail = $form->get('email')->getData();
            if (!in_array($mail, $event->getMailsToRemind())){
                $msg = $translator->trans('This mail is not in the list of contacts to call back');
                $this->addFlash('warning', $msg);
            }else{
                $msg = $translator->trans('You have removed the mail : ') .$mail. $translator->trans(' from the list of contacts to call back');
                $event->removeMailToRemind($mail);
                $em->persist($event);
                $em->flush();
                $this->addFlash('success', $msg);
            }
            return $this->redirectToRoute('events_home');
        }

        $redirect_link = $this->redirectToRoute('events_home')->getTargetUrl();
        $form_title = $translator->trans('You are about to unsubscribe for the event reminder : ') .'<b>'. $event->getTitle() .'</b>';
        return $this->render('themes/just_the_form.html.twig',[
            'form' => $form->createView(),
            'form_title' => $form_title,
            'redirect' => [
                'link' => $redirect_link,
                'txt' => $this->translator->trans('Cancel'),
            ]
        ]);
    }


    /**
     * @Route("/image/delete/{id}", name="image_delete")
     */
    public function deleteImg($id, Event $event, EntityManagerInterface $em, Request $request): Response
    {
        $event->setImage(null);
        $em->flush();
        $this->addFlash('info', $this->translator->trans('Image removed successfully'));
        return $this->redirect($request->headers->get('referer'));
    }

}
