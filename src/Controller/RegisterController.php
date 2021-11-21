<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * RegisterController constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em =$em;
    }

    /**
     * @Route("/inscription", name="register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $notification = '';
        $notifStatus = '';

        if ($this->getUser()) {
            return $this->redirectToRoute('account');
        }

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            /** @var User $user */
            $user = $form->getData();
            $searchEmail = $this->em->getRepository(User::class)->findOneByEmail($user->getEmail());

            if (!$searchEmail)
            {
                // Encode le mot de passe du User
                $encodingPassword = $encoder->encodePassword($user, $user->getPassword());
                $user->setPassword($encodingPassword);
                // persist le membre
                $this->em->persist($user);
                $this->em->flush();

                // Envoi de confirmation d'inscription
                $mail = new Mail();
                $content = "Bonjour ".$user->getFirstname()."</br>Bienvenue sur La Boutique Antillaise.</br></br>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aliquam autem delectus deserunt ducimus enim est.</br></br>Id inventore laudantium molestias mollitia nihil nisi odit praesentium, quam quia quibusdam sunt suscipit veritatis.";
                $mail->send(
                    $user->getEmail(),
                    $user->getFullName(),
                    'Bienvenue sur La Boutique Antillaise',
                    $content);

                $notification = "Votre inscription s'est correctement déroulée. Vous pouvez vous connecter à votre compte.";
                $notifStatus = "-success";
            } else {
                $notification = "L'email que vous avez renseigné existe déjà.";
                $notifStatus = "-danger";

            }

        }

        return $this->render('register/index.html.twig', [
            'registerForm'  => $form->createView(),
            'notification'  => $notification,
            'notifStatus'   => $notifStatus,
        ]);
    }
}
