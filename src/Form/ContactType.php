<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new Assert\NotBlank(message: 'Votre nom est requis.'),
                    new Assert\Length(min: 2, minMessage: '2 caractères minimum')
                ],
                'attr' => ['class' => 'form-control bg-dark text-white border-secondary']
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new Assert\NotBlank(message: 'Votre email est requis.'),
                    new Assert\Email(message: 'Email invalide.')
                ],
                'attr' => ['class' => 'form-control bg-dark text-white border-secondary']
            ])
            ->add('sujet', TextType::class, [
                'label' => 'Sujet',
                'constraints' => [
                    new Assert\NotBlank(message: 'Le sujet est requis.'),
                    new Assert\Length(min: 5, minMessage: 'Sujet trop court.')
                ],
                'attr' => ['class' => 'form-control bg-dark text-white border-secondary']
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message',
                'constraints' => [
                    new Assert\NotBlank(message: 'Le message est requis.'),
                    new Assert\Length(min: 20, minMessage: 'Merci de détailler votre message (20 caractères min).')
                ],
                'attr' => [
                    'rows' => 6,
                    'class' => 'form-control bg-dark text-white border-secondary'
                ]
            ])
            // Honeypot simple (anti-spam)
            ->add('website', TextType::class, [
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'd-none', 'tabindex' => '-1', 'autocomplete' => 'off']
            ])
            ->add('consent', CheckboxType::class, [
                'label' => 'J’accepte le traitement de mes données conformément à la politique de confidentialité.',
                'mapped' => false,
                'constraints' => [new Assert\IsTrue(message: 'Vous devez accepter pour continuer.')]
            ])
            ->add('envoyer', SubmitType::class, [
                'label' => 'Envoyer',
                'attr' => ['class' => 'btn btn-primary text-white']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // no data class; we just transport values
        ]);
    }
}
