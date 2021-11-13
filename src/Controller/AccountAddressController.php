<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountAddressController extends AbstractController
{
    public $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/account/adresses", name="account_address")
     */
    public function index(): Response
    {
        return $this->render('account/address.html.twig', );
    }

    /**
     * @Route("/account/ajout-adresse", name="account_address_add")
     */
    public function add(Request $request): Response
    {
        $address = new Address();

        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $address->setUser($this->getUser());

            $this->em->persist($address);
            $this->em->flush();

            return $this->redirectToRoute('account_address');
        }

        return $this->render('account/address_form.html.twig', [
            'form'  => $form->createView(),
        ]);
    }

    /**
     * @Route("/account/modifier-adresse/{id}", name="account_address_edit")
     */
    public function edit(Request $request, $id): Response
    {
        $address = $this->em->getRepository(Address::class)->findOneById($id);

        if (!$address || $address->getUser() != $this->getUser())
        {
            return $this->redirectToRoute('account_address');
        }

        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->em->flush();
            return $this->redirectToRoute('account_address');
        }

        return $this->render('account/address_form.html.twig', [
            'form'  => $form->createView(),
        ]);
    }

    /**
     * @Route("/account/supprimer-adresse/{id}", name="account_address_delete")
     */
    public function delete($id): Response
    {
        $address = $this->em->getRepository(Address::class)->findOneById($id);

        if ($address && $address->getUser() == $this->getUser())
        {
            $this->em->remove($address);
            $this->em->flush();
        }

        return $this->redirectToRoute('account_address');

    }
}
