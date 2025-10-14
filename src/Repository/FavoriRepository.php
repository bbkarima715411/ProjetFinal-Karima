<?php

namespace App\Repository;

use App\Entity\Favori;
use App\Entity\Lot;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Favori>
 */
class FavoriRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Favori::class);
    }

    public function isFavorite(User $user, Lot $lot): bool
    {
        return (bool) $this->createQueryBuilder('f')
            ->andWhere('f.user = :u')->setParameter('u', $user)
            ->andWhere('f.lot = :l')->setParameter('l', $lot)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.user = :u')->setParameter('u', $user)
            ->orderBy('f.createdAt', 'DESC')
            ->getQuery()->getResult();
    }
}
