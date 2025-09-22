<?php

namespace App\Form;

use App\Entity\Lot;
use App\Entity\EvenementEnchere;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class LotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ⇩ Ces noms correspondent EXACTEMENT à tes propriétés (avec majuscules)
            ->add('Lot', TextType::class, [
                'required' => false,
                'label'    => 'Nom du lot',
            ])
            ->add('Categorie', TextType::class, [
                'required' => false,
                'label'    => 'Catégorie',
            ])
            ->add('Paiement', NumberType::class, [
                'required' => false,
                'label'    => 'Paiement / Prix',
                'scale'    => 2,
            ])
            ->add('Facture', TextType::class, [
                'required' => false,
                'label'    => 'Facture',
            ])
            ->add('evenementEnchere', EntityType::class, [
                'class' => EvenementEnchere::class,
                'choice_label' => 'id', // change en 'titre' si ton entity a un champ titre
                'placeholder'  => 'Sélectionner un événement',
                'label'        => 'Événement d’enchère',
            ])

            // 📷 Champ fichier NON mappé (on gère le move() dans le contrôleur)
            ->add('imageFile', FileType::class, [
                'mapped'   => false,
                'required' => false,
                'label'    => 'Image (JPEG / PNG / WebP)',
                'constraints' => [
                    new File([
                        'maxSize' => '4M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                        'mimeTypesMessage' => 'Upload une image JPEG/PNG/WebP (max 4 Mo).',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lot::class,
        ]);
    }
}
