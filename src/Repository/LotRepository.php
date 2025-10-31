<?php

namespace App\Repository;

use App\Entity\Lot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * @extends ServiceEntityRepository<Lot>
 */
class LotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lot::class);
    }

    /** 
     * Récupère les derniers lots avec leurs événements d'enchères
     * @return Lot[] 
     */
    public function findLatest(int $limit = 20): array
    {
        return $this->createQueryBuilder('l')
            ->leftJoin('l.evenementEnchere', 'e')
            ->addSelect('e')
            ->andWhere('e IS NOT NULL') // Ne récupère que les lots avec un événement valide
            ->orderBy('l.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère tous les lots avec un événement d'enchères valide
     * @return Lot[]
     */
    public function findAllWithValidEvent()
    {
        return $this->createQueryBuilder('l')
            ->leftJoin('l.evenementEnchere', 'e')
            ->addSelect('e')
            ->andWhere('e IS NOT NULL') // Ne récupère que les lots avec un événement valide
            ->orderBy('l.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Supprime les lots orphelins (sans événement d'enchères)
     * @return int Nombre de lots supprimés
     */
    public function removeOrphanedLots(): int
    {
        return $this->createQueryBuilder('l')
            ->delete()
            ->where('l.evenementEnchere IS NULL')
            ->getQuery()
            ->execute();
    }
}
