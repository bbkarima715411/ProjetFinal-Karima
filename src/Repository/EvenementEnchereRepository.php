<?php

namespace App\Repository;

use App\Entity\EvenementEnchere;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EvenementEnchere>
 */
class EvenementEnchereRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EvenementEnchere::class);
    }

    /** @return EvenementEnchere[] */
    public function findEnCours(int $limit = 8): array
    {
        // Utilise les colonnes que TU as dans l’entité (titre, debutAt, finAt, statut)
        // Si pas encore de dates/statut en BDD, contente-toi d’un orderBy id DESC :
        return $this->createQueryBuilder('e')
            ->orderBy('e.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
