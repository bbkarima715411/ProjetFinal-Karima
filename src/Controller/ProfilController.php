<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        // Nécessite un utilisateur connecté
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Formulaire simple d'édition du profil
        $form = $this->createFormBuilder($user)
            ->add('firstName', TextType::class, [
                'required' => false,
                'label' => 'Prénom',
            ])
            ->add('lastName', TextType::class, [
                'required' => false,
                'label' => 'Nom',
            ])
            ->add('phone', TelType::class, [
                'required' => false,
                'label' => 'Téléphone',
            ])
            ->add('address1', TextType::class, [
                'required' => false,
                'label' => 'Adresse',
            ])
            ->add('address2', TextType::class, [
                'required' => false,
                'label' => 'Complément d\'adresse',
            ])
            ->add('postalCode', TextType::class, [
                'required' => false,
                'label' => 'Code postal',
            ])
            ->add('city', TextType::class, [
                'required' => false,
                'label' => 'Ville',
            ])
            ->add('country', TextType::class, [
                'required' => false,
                'label' => 'Pays',
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Profil mis à jour.');

            return $this->redirectToRoute('app_profil');
        }

        // Mode édition activé via paramètre de requête ?edit=1 ou si le formulaire est soumis
        $editMode = $request->query->getBoolean('edit', false) || $form->isSubmitted();

        return $this->render('profil/index.html.twig', [
            'user' => $user,
            'profil_form' => $form->createView(),
            'edit_mode' => $editMode,
        ]);
    }
}
