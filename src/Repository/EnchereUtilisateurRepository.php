<?php

namespace App\Repository;

use App\Entity\EnchereUtilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EnchereUtilisateur>
 *
 * @method EnchereUtilisateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method EnchereUtilisateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method EnchereUtilisateur[]    findAll()
 * @method EnchereUtilisateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnchereUtilisateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EnchereUtilisateur::class);
    }

//    /**
//     * @return EnchereUtilisateur[] Returns an array of EnchereUtilisateur objects
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
