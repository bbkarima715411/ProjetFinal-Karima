<?php

namespace App\Repository;

use App\Entity\Lot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lot::class);
    }

    /** Lots d’événements ouverts (optionnel) */
    public function trouverLotsActifs(int $limit = 30): array
    {
        $now = new \DateTimeImmutable();

        return $this->createQueryBuilder('l')
            ->join('l.evenementEnchere', 'e')
            ->andWhere('e.statut = :open')
            ->andWhere('e.debutAt <= :now')
            ->andWhere('e.finAt > :now')
            ->setParameter('open', 'open')
            ->setParameter('now', $now)
            ->orderBy('l.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}

