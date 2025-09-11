<?php

namespace App\Repository;

use App\Entity\EvenementEnchere;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EvenementEnchere>
 *
 * @method EvenementEnchere|null find($id, $lockMode = null, $lockVersion = null)
 * @method EvenementEnchere|null findOneBy(array $criteria, array $orderBy = null)
 * @method EvenementEnchere[]    findAll()
 * @method EvenementEnchere[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvenementEnchereRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EvenementEnchere::class);
    }

//    /**
//     * @return EvenementEnchere[] Returns an array of EvenementEnchere objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?EvenementEnchere
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
