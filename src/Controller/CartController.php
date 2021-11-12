<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{

    private $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    /**
     * Vers le panier
     *
     * @Route("/mon-panier", name="cart")
     */
    public function index(Cart $cart): Response
    {
        $procuctsInCart = [];

        foreach ($cart->get() as $id => $quantity) {
            $procuctsInCart[] = [
                'product'   => $this->em->getRepository(Product::class)->findOneById($id),
                'quantity'  => $quantity,
            ];
        }

        return $this->render('cart/index.html.twig', [
            'cart' => $procuctsInCart,
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
     * Vide le panier
     *
     * @Route("/cart/remove", name="remove_my_cart")
     */
    public function remove(Cart $cart): Response
    {
        $cart->remove();

        return $this->redirectToRoute('products');
    }
}
