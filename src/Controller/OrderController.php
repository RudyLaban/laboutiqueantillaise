<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Address;
use App\Entity\Carrier;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Entity\Product;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
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

        return $this->render('order/index.html.twig', [
            'form'  => $form->createView(),
            'cart'  => $cart->getFullCart(),
        ]);
    }

    /**
     * @Route("/commande/recapitulatif", name="order_recap", methods={"POST"})
     */
    public function add(Request $request, Cart $cart): Response
    {
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $date = new \DateTimeImmutable();

            /** @var Carrier $carrier */
            $carrier = $form->get('carriers')->getData();
            /** @var Address $delivery */
            $delivery = $form->get('addresses')->getData();

            $deliveryAddress = $delivery->getFirstname().' '. $delivery->getLastname();
            $deliveryAddress .= '<br/>'.$delivery->getPhone();

            if ($delivery->getCompany())
            {
                $deliveryAddress .= '<br/>'.$delivery->getCompany();
            }

            $deliveryAddress .= '<br/>'.$delivery->getAddress();
            $deliveryAddress .= '<br/>'.$delivery->getPostal().', '. $delivery->getCity();
            $deliveryAddress .= '<br/>'.$delivery->getCountry();

            // enregistrer ma commande  : Order()
            $order = new Order();
            $order->setUser($this->getUser());
            $order->setCreatedAt($date);
            $order->setCarrierName($carrier->getName());
            $order->setCarrierPrice($carrier->getPrice());
            $order->setDelivery($deliveryAddress);
            $order->setPaid(false);

            $this->em->persist($order);

            // enregistrer mes produits : OrderDetails()
            foreach ($cart->getFullCart() as $productInCart)
            {
                /** @var Product $product */
                $product = $productInCart['product'];

                $orderDetails = new OrderDetails();
                $orderDetails->setUserOrder($order);
                $orderDetails->setProduct($product->getName());
                $orderDetails->setQuantity($productInCart['quantity']);
                $orderDetails->setPrice($product->getPrice());
                $orderDetails->setTotal($product->getPrice() * $productInCart['quantity']);

                $this->em->persist($orderDetails);
            }

            // $this->em->flush();

            return $this->render('order/add.html.twig', [
                'cart'      => $cart->getFullCart(),
                'carrier'   => $carrier,
                'delivery'  => $deliveryAddress,

            ]);
        }

        return $this->redirectToRoute('cart');
    }
}
