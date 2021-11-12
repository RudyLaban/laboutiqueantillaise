<?php

namespace App\Controller;

use App\Classe\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * Vers le panier
     *
     * @Route("/mon-panier", name="cart")
     */
    public function index(Cart $cart): Response
    {
        return $this->render('cart/index.html.twig', [
            'cart' => $cart->getFullCart(),
        ]);
    }

    /**
     * Ajout au panier
     *
     * @Route("/cart/add/{id}", name="add_to_cart")
     */
    public function add(Cart $cart, $id): Response
    {
        $cart->add($id);

        return $this->redirectToRoute('cart');
    }

    /**
     * Enlève un élement d'un produit
     *
     * @Route("/cart/subtract/{id}", name="subtract_product")
     */
    public function subtract(Cart $cart, $id): Response
    {
        $cart->subtract($id);

        return $this->redirectToRoute('cart');
    }

    /**
     * Vide le panier
     *
     * @Route("/cart/remove", name="remove_my_cart")
     */
    public function remove(Cart $cart): Response
    {
        $cart->remove();

        return $this->redirectToRoute('products');
    }

    /**
     * Supprime un produit du pannier
     *
     * @Route("/cart/delete/{id}", name="delete_from_cart")
     */
    public function delete(Cart $cart, $id): Response
    {
        $cart->delete($id);

        return $this->redirectToRoute('cart');
    }
}
