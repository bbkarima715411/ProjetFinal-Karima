<?php

namespace App\Repository;

use App\Entity\EnchèreUtulisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EnchèreUtulisateur>
 *
 * @method EnchèreUtulisateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method EnchèreUtulisateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method EnchèreUtulisateur[]    findAll()
 * @method EnchèreUtulisateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnchèreUtulisateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EnchèreUtulisateur::class);
    }

//    /**
//     * @return EnchèreUtulisateur[] Returns an array of EnchèreUtulisateur objects
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

//    public function findOneBySomeField($value): ?EnchèreUtulisateur
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
