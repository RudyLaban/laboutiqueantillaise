<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Form\OrderType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    /**
     * @Route("/commande", name="order")
     */
    public function index(Request $request, Cart $cart): Response
    {
        if (empty($this->getUser()->getAddresses()->getValues()))
        {
            return $this->redirectToRoute('account_address_add');
        }

        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser(),
        ]);

        if ($form->isSubmitted() && $form->isValid())
        {
            $form->handleRequest($request);
        }

        return $this->render('order/index.html.twig', [
            'form'  => $form->createView(),
            'cart'  => $cart->getFullCart(),
        ]);
    }
}
