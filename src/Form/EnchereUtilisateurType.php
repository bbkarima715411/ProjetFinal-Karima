<?php

namespace App\Form;

use App\Entity\EnchereUtilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire CRUD pour l'entité `EnchereUtilisateur` (usage admin).
 *
 * Ne gère pas l'assignation du `user` connecté: ceci relève du flux métier.
 */
class EnchereUtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('montant')
            ->add('lot')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EnchereUtilisateur::class,
        ]);
    }
}
