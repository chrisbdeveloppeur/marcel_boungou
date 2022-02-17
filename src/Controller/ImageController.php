<?php

namespace App\Controller;

use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/image", name="image_")
 */
class ImageController extends AbstractController
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function deleteImg($id, Event $event, EntityManagerInterface $em, Request $request): Response
    {
        $event->setImage(null);
        $em->flush();
        $this->addFlash('info', $this->translator->trans('Image removed successfully'));
        return $this->redirect($request->headers->get('referer'));
    }
}
