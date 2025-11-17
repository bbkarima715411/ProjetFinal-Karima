<?php

namespace App\Form;

use App\Entity\Lot;
use App\Entity\EvenementEnchere;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeImmutableType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class LotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champs “propres”
            ->add('titre', TextType::class, [
                'required' => false,
                'label' => 'Titre'
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'label' => 'Description'
            ])
            ->add('categorie', TextType::class, [
                'required' => false,
                'label' => 'Catégorie'
            ])
            ->add('prixDepart', MoneyType::class, [
                'currency' => 'EUR',
                'label' => 'Prix de départ'
            ])
            ->add('incrementMin', NumberType::class, [
                'scale' => 2,
                'label' => 'Incrément minimum'
            ])
            ->add('prixAchatImmediat', MoneyType::class, [
                'required' => false,
                'currency' => 'EUR',
                'label' => 'Prix d\'achat immédiat (optionnel)'
            ])
            ->add('estimationMin', MoneyType::class, [
                'required' => false,
                'currency' => 'EUR',
                'label' => 'Estimation minimale (optionnel)'
            ])
            ->add('estimationMax', MoneyType::class, [
                'required' => false,
                'currency' => 'EUR',
                'label' => 'Estimation maximale (optionnel)'
            ])
            ->add('dateFin', DateTimeImmutableType::class, [
                'required' => false,
                'label' => 'Fin des enchères',
                'widget' => 'single_text'
            ])
            ->add('estVendu', CheckboxType::class, [
                'required' => false,
                'label' => 'Vendu ?'
            ])

            // Upload image (non mappé)
            ->add('imageFile', FileType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Image (JPEG/PNG/WebP)',
                'constraints' => [
                    new File([
                        'maxSize' => '4M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                        'mimeTypesMessage' => 'Upload une image JPEG/PNG/WebP (max 4 Mo).',
                    ]),
                ],
            ])

            // Lier à un événement
            ->add('evenementEnchere', EntityType::class, [
                'class' => EvenementEnchere::class,
                'choice_label' => 'titre',
                'label' => 'Événement d’enchère'
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
