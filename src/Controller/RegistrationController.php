<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Cart;
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
    #[Route('/inscription', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_accueil');
        }
        
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Encoder le mot de passe
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                // Définir le rôle par défaut
                $user->setRoles(['ROLE_USER']);
                
                // Définir l'email (déjà mappé par défaut)
                $user->setEmail($form->get('email')->getData());
                
                // Définir les champs supplémentaires
                $user->setFirstName($form->get('firstName')->getData());
                $user->setLastName($form->get('lastName')->getData());
                $user->setAddress1($form->get('address1')->getData());
                
                // Champs optionnels
                $user->setAddress2($form->get('address2')->getData() ?? null);
                $user->setPostalCode($form->get('postalCode')->getData());
                $user->setCity($form->get('city')->getData());
                $user->setCountry($form->get('country')->getData());
                $user->setPhone($form->get('phone')->getData() ?? null);
                $user->setBirthDate($form->get('birthDate')->getData() ?? null);

                // Créer un panier pour l'utilisateur
                $cart = new Cart();
                $cart->setUser($user);
                $user->setCart($cart);

                $entityManager->persist($user);
                $entityManager->persist($cart);
                $entityManager->flush();

                // Message de confirmation + redirection vers la connexion
                $name = trim($user->getFirstName() . ' ' . $user->getLastName());
                $displayName = $name ?: $user->getEmail();
                
                $this->addFlash('success', sprintf(
                    'Bienvenue %s. Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter.',
                    $displayName
                ));
                
                return $this->redirectToRoute('app_login');
                
            } catch (\Exception $e) {
                // En cas d'erreur, afficher un message d'erreur
                $this->addFlash('error', 'Une erreur est survenue lors de la création de votre compte. Veuillez réessayer.');
                
                // Journaliser l'erreur pour le débogage
                error_log('Erreur lors de l\'inscription : ' . $e->getMessage());
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
