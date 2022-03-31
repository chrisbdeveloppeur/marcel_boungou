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
 * @Route("/event", name="event_")
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
     * @Route("/index", name="index", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function index(EventRepository $eventRepository): Response
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
            return $this->redirectToRoute('event_home', [], Response::HTTP_SEE_OTHER);
        }

        $redirect_link = $this->redirectToRoute('event_home')->getTargetUrl();
        return $this->render('themes/just_the_form.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
            'form_title' => $this->translator->trans('Add new Event'),
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
        }

        $redirect_link = $this->redirectToRoute('event_home')->getTargetUrl();
        return $this->render('themes/just_the_form.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
            'form_title' => $this->translator->trans('Edit Event'),
            'redirect' => [
                'link' => $redirect_link,
                'txt' => $this->translator->trans('Back to Events home'),
            ]
        ]);
    }

    /**
     * @Route("/{id}/delete", name="delete", methods={"POST", "GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function delete(Request $request, $id, EntityManagerInterface $entityManager, EventRepository $eventRepository): Response
    {
        $event = $eventRepository->find($id);
        $title = $event->getTitle();
        $entityManager->remove($event);
        $entityManager->flush();
        $this->addFlash('warning', $this->translator->trans('Element deleted successfuly : ').'<b>'.$title.'</b>');
        return $this->redirectToRoute('event_index');
    }


    /**
     * @Route("/reminder/add/{id}/{email?}", name="add_email_reminder", methods={"POST","GET"})
     */
    public function addEmailReminder($id, EventRepository $eventRepository, TranslatorInterface $translator, EntityManagerInterface $em, Request $request, $email){
        $event = $eventRepository->find($id);

        if ($_POST){
            $mail = $_POST['email'];
        }else if($email){
            $mail = $email;
        }

        if ($mail){
            if (in_array($mail, $event->getMailsToRemind())){
                $msg = $translator->trans('You are already in the list of contacts to call back');
                $this->addFlash('warning', $msg);
            }else{
                $msg = $translator->trans('Thanks you ! You will be notified by mail when the event ').'<b>'.$event.'</b>'.$this->translator->trans(' date is approaching');
                $event->addMailToRemind($mail);
                $em->persist($event);
                $em->flush();
                $this->addFlash('success', $msg);
            }
            $previousUrl = $request->headers->get('referer');
            return $this->redirect($previousUrl);
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
                $msg = $translator->trans('The Email ').'<b>'.$mail.'</b>'. $translator->trans(' will no longer receive reminders about the event ').'<b>'.$event->getTitle().'</b><br><span class="help">'.$this->translator->trans('You reconsider your decision ?').'<a href="'.$this->redirectToRoute('event_add_email_reminder',['id'=> $event->getId(), 'email'=> $mail])->getTargetUrl().'">'.$this->translator->trans(' Click here').'</a></span>';
                $event->removeMailToRemind($mail);
                $em->persist($event);
                $em->flush();
                $this->addFlash('success', $msg);
            }
            return $this->redirectToRoute('event_home');
        }

        $redirect_link = $this->redirectToRoute('event_home')->getTargetUrl();
        $form_info = $translator->trans('You are about to unsubscribe for the event reminder : ') .'<b>'. $event->getTitle() .'</b>';
        return $this->render('themes/just_the_form.html.twig',[
            'form' => $form->createView(),
            'form_info' => $form_info,
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
