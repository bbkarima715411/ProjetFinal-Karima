<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('format_currency', [$this, 'formatCurrency']),
        ];
    }

    public function formatCurrency($number, $currency = '€', $decimals = 2, $decimalSeparator = ',', $thousandsSeparator = ' ')
    {
        $formatted = number_format($number, $decimals, $decimalSeparator, $thousandsSeparator);
        
        // Format selon la devise
        switch (strtoupper($currency)) {
            case 'EUR':
            case '€':
                return $formatted . ' €';
            case 'USD':
            case '$':
                return '$' . $formatted;
            case 'GBP':
            case '£':
                return '£' . $formatted;
            default:
                return $formatted . ' ' . $currency;
        }
    }
}
