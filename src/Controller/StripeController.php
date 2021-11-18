<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    /**
     * @Route("/commande/create-session/{reference}", name="stripe_create_session")
     */
    public function index(EntityManagerInterface $em, Cart $cart, $reference): Response
    {
        $productForStripe = [];
        /** @var Order $order */
        $order = $em->getRepository(Order::class)->findOneByReference($reference);

        // Intégration de Stripe pour le paiement
        Stripe::setApiKey('sk_test_51JwOlZI9YJgCO4WBQr33uui3teAvdFPWhAX0FWT3wg0vXesIs1Ndxy6aWO1iQ6VjjOH3A7YPqYKKD7sCV2pvnkZI00eJnE1uqX');

        // construction du tableau de produit à fournir à la Session Stripe
        foreach ($order->getOrderDetails()->getValues() as $orderDetails)
        {
            $product = $em->getRepository(Product::class)->findOneByName($orderDetails->getProduct());

            $productForStripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $orderDetails->getPrice(),
                    'product_data' => [
                        'name' => $orderDetails->getProduct(),
                        'images' => [$_SERVER['SYMFONY_APPLICATION_DEFAULT_ROUTE_URL']."uploads/".$product->getIllustration()],
                    ],
                ],
                'quantity' => $orderDetails->getQuantity(),
            ];
        }

        $productForStripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $order->getCarrierPrice(),
                'product_data' => [
                    'name' => $order->getCarrierName(),
                    'images' => [$_SERVER['SYMFONY_APPLICATION_DEFAULT_ROUTE_URL']],
                ],
            ],
            'quantity' => 1,
        ];

        $checkoutSession = Session::create([
            'line_items' => [[
                # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
                $productForStripe,
            ]],
            'payment_method_types' => [
                'card',
            ],
            'customer_email' => $this->getUser()->getEmail(),
            'mode' => 'payment',
            'success_url' => $_SERVER['SYMFONY_APPLICATION_DEFAULT_ROUTE_URL'] . 'commande/merci/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $_SERVER['SYMFONY_APPLICATION_DEFAULT_ROUTE_URL'] . 'commande/erreur/{CHECKOUT_SESSION_ID}',

        ]);

        $order->setStripeSessionId($checkoutSession->id);
        $em->flush();

        return $this->redirect($checkoutSession->url);
    }
}
