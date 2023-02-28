<?php

namespace App\Controller;


use App\Mailing\MailerController;
use App\Entity\Biography;
use App\Entity\Message;
use App\Entity\Subscriber;
use App\Form\ContactType;
use App\Form\SubscriberType;
use App\Repository\BiographyRepository;
use App\Repository\SubscriberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/", name="home_")
 */
class HomeController extends AbstractController
{

    private $translator;
    private $mailer;

    public function __construct(TranslatorInterface $translator, MailerController $mailer)
    {
        $this->translator = $translator;
        $this->mailer = $mailer;
    }

    /**
     * @Route("/", name="index")
     */
    public function home(Request $request, EntityManagerInterface $em, BiographyRepository $biographyRepository): Response
    {

        // SUBSCRIBER FORM CONTROL
        $subscriber = new Subscriber();
        $form = $this->createForm(SubscriberType::class, $subscriber);
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            $message = new Message();
            $resultSendMail = $this->mailer->sendMessageConfirmationSubNews($message, $form);
            if ($form->isValid() && $resultSendMail){
                $em->persist($subscriber);
                $em->flush();
                $this->addFlash('info', $this->translator->trans('Thank you for subscribing to the newsletter. A confirmation email has just been sent to the email address indicated.'));
            }else{
                $errMsg = null;
                if ($form->get('email')->getErrors()->current()){
                    $errMsg = $form->get('email')->getErrors()->current()->getMessage();
                    $errMsg = '<br>'.$errMsg;
                }
                $this->addFlash('danger', $this->translator->trans('Operation failed').$errMsg);
            }
            $url = $this->redirectToRoute('home_index')->getTargetUrl().'#subscribeForm';
            return $this->redirect($url);
        }

        // CONTACT FORM CONTROL
        $message = new Message();
        $contactForm = $this->createForm(ContactType::class, $message);
        $contactForm->handleRequest($request);
        if ($contactForm->isSubmitted()){
            if ($contactForm->isValid()){
                $em->persist($message);
                $em->flush();
                $this->mailer->sendMessageContact($message);
                $this->addFlash('info', $this->translator->trans('Thank you for your message !'));
            }else{
                $fields = $contactForm->all();
                $errorsMsgs = [];
                foreach ($fields as $field){
                    if ($field->getErrors()->count()){
                        $errorsField =  $field->getErrors();
                        foreach ($errorsField as $errorField){
                            $errorsMsgs[] = $errorField->getMessage();
                        }
                    }
                }
//                dd(implode("<br>", $errorsMsgs));
                $this->addFlash('danger', $this->translator->trans('Operation failed').'<br>'.implode("<br>", $errorsMsgs));
            }
            $url = $this->redirectToRoute('home_index')->getTargetUrl().'#contact';
            return $this->redirect($url);
        }

        // BIOGRAPHY CHECK / CONTROL
        $bios = $biographyRepository->findAll();
        $bioNb = count($bios);
        if ($bioNb > 0){
            $biography = $bios[0];
        }else{
            $biography = new Biography();
            $em->persist($biography);
            $em->flush();
        }

        return $this->render('home/index.html.twig',[
            'subs_form' => $form->createView(),
            'contact_form' => $contactForm->createView(),
            'biography' => $biography,
        ]);
    }
}
