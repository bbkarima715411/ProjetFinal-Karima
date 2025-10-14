<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Formulaire minimal pour déposer une offre.
 *
 * Ne contient que le champ `montant` en euros, la logique d'affectation Lot/User
 * est gérée côté contrôleur/service.
 */
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
