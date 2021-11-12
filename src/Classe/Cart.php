<?php

namespace App\Classe;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Représentation objet du panier
 */
class Cart
{
    private $session;

    private $em;

    /**
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session, EntityManagerInterface $em)
    {
        $this->session = $session;
        $this->em = $em;
    }


    /**
     * Ajout d'un produit au panier
     *
     * @param $id
     */
    public function add($id)
    {
        $cart = $this->session->get('cart', []);

        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        $this->session->set('cart', $cart);
    }

    /**
     * Enlève un produit du panier
     *
     * @param $id
     * @return mixed|void
     */
    public function subtract($id)
    {
        $cart = $this->session->get('cart');
        // si la quantité du produit est à 1, on le supprime
        if ($cart[$id] == 1){
            return $this->delete($id);
        }

        $cart[$id]--;
        $this->session->set('cart', $cart);
    }

    /**
     * Renvoi le panier 'cart' stocké en session
     *
     * @return mixed
     */
    public function get()
    {
        return $this->session->get('cart');
    }

    /**
     * Supprime le panier de la session
     *
     * @return mixed
     */
    public function remove()
    {
        return $this->session->remove('cart');
    }

    /**
     * Supprime un produit du pannier
     *
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        $cart = $this->session->get('cart', []);
        unset($cart[$id]);

        return $this->session->set('cart', $cart);
    }

    /**
     * Renvoi le panier complet, avec les produits qu'il contient
     *
     * @return array
     */
    public function getFullCart()
    {
        $completeCart = [];

        if ($this->get())
        {
            foreach ($this->get() as $id => $quantity)
            {
                $product = $this->em->getRepository(Product::class)->findOneById($id);

                if (!$product)
                {
                    $this->delete($id);
                    continue;
                }

                $completeCart[] = [
                    'product'   => $product,
                    'quantity'  => $quantity,
                ];
            }
        }

        return $completeCart;
    }
}