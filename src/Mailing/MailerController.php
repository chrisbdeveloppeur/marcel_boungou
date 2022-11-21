<?php

namespace App\Mailing;

use App\Entity\Event;
use App\Entity\Message;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerController
{
    private $mailerInterface;

    public function __construct(MailerInterface $mailerInterface)
    {
        $this->mailerInterface = $mailerInterface;
    }

    public function sendMessageContact(Message $message)
    {
        $email = (new TemplatedEmail())
            ->from('contact@marcel-boungou.com')
            ->to('contact@marcel-boungou.com')
            ->subject('Message de '.$message->getSender())
            ->htmlTemplate('emails/message.html.twig', [
                'message' => $message,
            ])
            ->context([
                'message' => $message,
            ])
        ;

        $this->mailerInterface->send($email);
    }

    /**
     * @param Message $message
     * @param FormInterface $form
     */
    public function sendMessageConfirmationSubNews(Message $message, FormInterface $form)
    {
        $email = (new TemplatedEmail())
            ->from('contact@marcel-boungou.com')
            ->to($form->get('email')->getData())
            ->subject('Confirmation d\'abonnement à la newsletter')
            ->htmlTemplate('emails/confirm_sub_news.html.twig', [
                'message' => $message,
            ])
            ->context([
                'message' => $message,
            ])
        ;

        $this->mailerInterface->send($email);
    }

    /**
     * @param Message $message
     * @param string $email
     * @param Event $event
     */
    public function sendMessageConfirmationSubEvent(Message $message, string $toEmail, Event $event)
    {
        $email = (new TemplatedEmail())
            ->from('contact@marcel-boungou.com')
            ->to($toEmail)
            ->subject('Confirmation - Rappels évenement '. $event->getTitle())
            ->htmlTemplate('emails/confirm_sub_event.html.twig', [
                'message' => $message,
                'event' => $event,
                'toEmail' => $toEmail,
            ])
            ->context([
                'message' => $message,
                'event' => $event,
                'toEmail' => $toEmail,
            ])
        ;

        $this->mailerInterface->send($email);
    }
}