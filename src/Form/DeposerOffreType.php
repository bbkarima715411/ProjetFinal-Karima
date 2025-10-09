<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;

class DeposerOffreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('montant', MoneyType::class, [
            'currency' => 'EUR',
            'label' => 'Votre offre (€)',
            'required' => true,
            'scale' => 2,
        ]);
    }
}
