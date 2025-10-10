<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use App\Security\AutoLoginAuthenticator;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, UserAuthenticatorInterface $userAuthenticator, AutoLoginAuthenticator $authenticator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // Copier les champs de profil (non mappés) vers l'entité User
            $user->setFirstName($form->get('firstName')->getData());
            $user->setLastName($form->get('lastName')->getData());
            $user->setAddress1($form->get('address1')->getData());
            $user->setAddress2($form->get('address2')->getData());
            $user->setPostalCode($form->get('postalCode')->getData());
            $user->setCity($form->get('city')->getData());
            $user->setCountry($form->get('country')->getData());
            $user->setPhone($form->get('phone')->getData());
            $user->setBirthDate($form->get('birthDate')->getData());

            $entityManager->persist($user);
            $entityManager->flush();

            // Message de confirmation + redirection vers la connexion
            $first = $user->getFirstName() ?: '';
            $last  = $user->getLastName() ?: '';
            $full  = trim($first.' '.$last);
            $name  = $full !== '' ? $full : $user->getEmail();
            $this->addFlash('success', sprintf('Bienvenue %s. Votre compte a été créé. Vous pouvez maintenant vous connecter.', $name));
            return $this->redirectToRoute('app_login');
        }

        // Soumis mais invalide: guider l'utilisateur
        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('danger', 'Veuillez corriger les champs requis avant de continuer.');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
