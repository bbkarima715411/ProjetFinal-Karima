<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'label_attr' => ['data-required' => '1'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir votre adresse email']),
                    new EmailConstraint(['message' => 'Adresse email invalide'])
                ],
                'attr' => ['class' => 'form-control bg-dark text-white border-secondary']
            ])
            // Champs de profil (non mappés pour l'instant; ajout BDD possible plus tard)
            ->add('firstName', TextType::class, [
                'label' => 'Prénom', 'mapped' => false,
                'constraints' => [new NotBlank(['message' => 'Veuillez renseigner votre prénom'])],
                'label_attr' => ['data-required' => '1'],
                'attr' => ['class' => 'form-control bg-dark text-white border-secondary', 'placeholder' => 'Ex: Marie']
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom', 'mapped' => false,
                'constraints' => [new NotBlank(['message' => 'Veuillez renseigner votre nom'])],
                'label_attr' => ['data-required' => '1'],
                'attr' => ['class' => 'form-control bg-dark text-white border-secondary', 'placeholder' => 'Ex: Dupont']
            ])
            ->add('address1', TextType::class, [
                'label' => 'Adresse', 'mapped' => false,
                'constraints' => [new NotBlank(['message' => 'Veuillez renseigner votre adresse'])],
                'label_attr' => ['data-required' => '1'],
                'attr' => ['class' => 'form-control bg-dark text-white border-secondary', 'placeholder' => 'N°, rue']
            ])
            ->add('address2', TextType::class, [
                'label' => 'Complément (optionnel)', 'required' => false, 'mapped' => false,
                'attr' => ['class' => 'form-control bg-dark text-white border-secondary']
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Code postal', 'mapped' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez renseigner votre code postal']),
                    new Regex(['pattern' => '/^[A-Za-z0-9\-\s]{3,10}$/', 'message' => 'Code postal invalide'])
                ],
                'label_attr' => ['data-required' => '1'],
                'attr' => ['class' => 'form-control bg-dark text-white border-secondary', 'placeholder' => 'Ex: 75001']
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville', 'mapped' => false,
                'constraints' => [new NotBlank(['message' => 'Veuillez renseigner votre ville'])],
                'label_attr' => ['data-required' => '1'],
                'attr' => ['class' => 'form-control bg-dark text-white border-secondary', 'placeholder' => 'Ex: Paris']
            ])
            ->add('country', CountryType::class, [
                'label' => 'Pays', 'mapped' => false,
                'placeholder' => 'Sélectionner un pays',
                'preferred_choices' => ['FR','BE','CH','LU'],
                'constraints' => [new NotBlank(['message' => 'Veuillez renseigner votre pays'])],
                'label_attr' => ['data-required' => '1'],
                'attr' => ['class' => 'form-select bg-dark text-white border-secondary']
            ])
            ->add('phone', TelType::class, [
                'label' => 'Téléphone', 'required' => false, 'mapped' => false,
                'constraints' => [
                    new Length(['min' => 7, 'minMessage' => 'Numéro trop court']),
                    new Regex(['pattern' => '/^[+0-9().\s-]{7,20}$/', 'message' => 'Numéro invalide'])
                ],
                'attr' => ['class' => 'form-control bg-dark text-white border-secondary', 'placeholder' => '+33 6 12 34 56 78', 'inputmode' => 'tel']
            ])
            ->add('birthDate', DateType::class, [
                'label' => 'Date de naissance', 'required' => false, 'mapped' => false,
                'widget' => 'single_text',
                'constraints' => [new LessThanOrEqual(['value' => 'today', 'message' => 'La date doit être passée'])],
                'attr' => ['class' => 'form-control bg-dark text-white border-secondary']
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les conditions.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
                'label_attr' => ['data-required' => '1']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
