<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
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
        if (!$order->getState() == 0)
        {
            // vider la session 'cart'
            $cart->remove();
            // persist la commande
            $order->setState(1);
            $this->em->flush();
            // envoi d'email au client
            $mail = new Mail();
            $content = "Bonjour ".$order->getUser()->getFirstname()."</br>Merci pour votre commande LBA n°".$order->getReference().".</br></br>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aliquam autem delectus deserunt ducimus enim est.</br></br>Id inventore laudantium molestias mollitia nihil nisi odit praesentium, quam quia quibusdam sunt suscipit veritatis.";
            $mail->send(
                $order->getUser()->getEmail(),
                $order->getUser()->getFullName(),
                'Votre commande LBA n°'.$order->getReference().' à bien été validée',
                $content);

        }

        // afficher les infos de la commande

        return $this->render('order_success/index.html.twig', [
            'order' => $order,
        ]);
    }
}
