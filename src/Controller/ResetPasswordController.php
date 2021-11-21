<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\ResetPassword;
use App\Entity\User;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordController extends AbstractController
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
     * @Route("/mot-de-passe-oublié", name="reset_password")
     */
    public function index(Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        if ($request->get('email')) {
            $user = $this->em->getRepository(User::class)->findOneByEmail($request->get('email'));

            if ($user)
            {
                // 1 : Enregistre en base la demande de reset user, token, createdAt
                $resetPwd = new ResetPassword();
                $resetPwd->setUser($user);
                $resetPwd->setToken(uniqid());
                $resetPwd->setCreatedAt(new \DateTimeImmutable());

                $this->em->persist($resetPwd);
                $this->em->flush();

                // 2 : Envoyer un mail à l'utilisateur
                $url = $this->generateUrl('update_password', ['token' => $resetPwd->getToken()]);
                $content = "Bonjour".$user->getFirstName().",<br><br>Vous avez demander à réinitialiser votre mot de passe sur La Boutique Antillaise.<br>";
                $content .= "Merci de bien vouloir <a href='$url'>cliquer ici</a> pour mettre à jour votre mot de passe.<br>";

                $mail = new Mail();
                $mail->send(
                    $user->getEmail(),
                    $user->getFullName(),
                    'Réinitialiser votre mot de passe sur La Boutique Antillaise',
                    $content
                );
                $this->addFlash('notice', 'Vous aller recevoir dans quelques secondes un mail avec la procédure pour réinitialiser votre mot de passe');
            } else {
                $this->addFlash('notice', 'Cette adresse email est inconnue');
            }
        }

        return $this->render('reset_password/index.html.twig'/*, [
            'controller_name' => 'ResetPasswordController',
        ]*/);
    }

    /**
     * @Route("/modifier-mon-mot-de-passe/{token}", name="update_password")
     */
    public function update(Request $request, $token, UserPasswordEncoderInterface $encoder): Response
    {
        /** @var ResetPassword $resetPwd */
        $resetPwd = $this->em->getRepository(ResetPassword::class)->findOneByToken($token);

        if (!$resetPwd)
        {
            return $this->redirectToRoute('reset_password');
        }
        // Vérifier que l'heure actuelle < à createdAt + 3H
        $createdAtPlus3Hours = $resetPwd->getCreatedAt()->modify('+ 5 hour');
        // $createdAtPlus3Hours = $resetPwd->getCreatedAt();
        $now = new \DateTimeImmutable();
        if ($now > $createdAtPlus3Hours)
        {
            $this->addFlash('notice', 'Votre demande réinitialisation de mot de passe à expirer. Merci de la renouveler');
            return $this->redirectToRoute('reset_password');
        }

        // Afficher la vue mots de passe et confirmation de mot de passe
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isSubmitted())
        {
            $user = $resetPwd->getUser();
            $newPwd = $form->get('new_password')->getData();
            // Encodage et flush du nouveau mot de passe
            $password = $encoder->encodePassword($user, $newPwd);

            $user->setPassword($password);
            $this->em->flush();

            $this->addFlash('notice', 'Votre mot de passe a bien été mis à jour');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
