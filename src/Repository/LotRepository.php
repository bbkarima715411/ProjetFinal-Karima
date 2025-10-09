<?php

namespace App\Repository;

use App\Entity\Lot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lot>
 */
class LotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lot::class);
    }

    /** @return Lot[] */
    public function findLatest(int $limit = 20): array
    {
        return $this->createQueryBuilder('l')
            ->orderBy('l.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
