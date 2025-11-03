<?php

namespace App\Service;

use App\Repository\LotRepository;

class CategoryResolver
{
    public function __construct(private LotRepository $lots) {}

    /** Slug maison, déterministe (mêmes règles pour génération et résolution) */
    public static function slugify(string $label): string
    {
        $s = mb_strtolower($label, 'UTF-8');
        $replacements = [
            'à'=>'a','á'=>'a','â'=>'a','ä'=>'a','å'=>'a',
            'ç'=>'c',
            'é'=>'e','è'=>'e','ê'=>'e','ë'=>'e',
            'ï'=>'i','î'=>'i','ì'=>'i','í'=>'i',
            'ô'=>'o','ö'=>'o','ò'=>'o','ó'=>'o',
            'ù'=>'u','ú'=>'u','û'=>'u','ü'=>'u',
            '&'=>'et', '\''=>'',
        ];
        $s = strtr($s, $replacements);
        $s = preg_replace('~[^a-z0-9]+~u', '-', $s);
        $s = trim($s, '-');
        return $s ?: 'categorie';
    }

    /** Retourne le libellé exact dont le slug correspond, ou null */
    public function findLabelBySlug(string $slug): ?string
    {
        foreach ($this->lots->findDistinctCategories() as $cat) {
            if (self::slugify($cat) === $slug) {
                return $cat;
            }
        }
        return null;
    }
}
