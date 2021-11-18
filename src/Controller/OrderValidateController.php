<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderValidateController extends AbstractController
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
     * @Route("/commande/merci/{stripeSessionId}", name="order_success")
     */
    public function index(Cart $cart, $stripeSessionId): Response
    {
        /** @var Order $order */
        $order = $this->em->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);
        // redirection si la commande n'existe pas ou si l'utilisateur courant n'est pas l'utilisateur de commande
        if (!$order || $order->getUser() != $this->getUser())
        {
            return $this->redirectToRoute('home');
        }

        // modif le champ paid de commande
        if (!$order->isPaid())
        {
            // vider la session 'cart'
            $cart->remove();
            // persist la commande
            $order->setPaid(true);
            $this->em->flush();
            // envoi d'email au client
        }

        // afficher les infos de la commande

        return $this->render('order_success/index.html.twig', [
            'order' => $order,
        ]);
    }
}
