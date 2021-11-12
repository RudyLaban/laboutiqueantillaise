<?php

namespace App\Classe;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Représentation objet du panier
 */
class Cart
{
    private $session;

    /**
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }


    /**
     * Ajout au panier
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
     * Renvoi le panier 'cart' stocké en session
     *
     * @return mixed
     */
    public function get() {
        return $this->session->get('cart');
    }

    /**
     * Supprime le panier de la session
     *
     * @return mixed
     */
    public function remove() {
        return $this->session->remove('cart');
    }
}