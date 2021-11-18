<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderCancelController extends AbstractController
{
    /** @var EntityManagerInterface $em */
    private $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/commande/erreur/{stripeSessionId}", name="order_cancel")
     */
    public function index($stripeSessionId): Response
    {
        /** @var Order $order */
        $order = $this->em->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        // redirection si l'order n'existe pas ou si le user courant n'est pas le user de order
        if (!$order || $order->getUser() != $this->getUser())
        {
            return $this->redirectToRoute('home');
        }

        // envoyer l'email d'échec de commande


        return $this->render('order_cancel/index.html.twig', [
            'order' => $order,
        ]);
    }
}
