<?php

namespace App\Twig;

use App\Repository\LotRepository;
use App\Service\CategoryResolver;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CategoriesExtension extends AbstractExtension
{
    public function __construct(private LotRepository $lots) {}

    public function getFunctions(): array
    {
        return [
            // Permet d'appeler categories_nav() dans Twig (navbar)
            new TwigFunction('categories_nav', [$this, 'categoriesNav']),
            // slugify côté Twig
            new TwigFunction('cat_slug', [CategoryResolver::class, 'slugify']),
        ];
    }

    public function categoriesNav(): array
    {
        return $this->lots->findDistinctCategories();
    }
}
