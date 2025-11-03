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

    /** Retourne la liste DISTINCT des catégories (strings) triées */
    public function findDistinctCategories(): array
    {
        $rows = $this->createQueryBuilder('l')
            ->select('DISTINCT l.Categorie AS cat')
            ->where('l.Categorie IS NOT NULL')
            ->orderBy('l.Categorie', 'ASC')
            ->getQuery()
            ->getArrayResult();

        return array_map(fn($r) => $r['cat'], $rows);
    }

    /** Retourne les lots d'une catégorie exacte (libellé tel qu'en BDD) */
    public function findByCategoryLabel(string $label): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.Categorie = :cat')
            ->setParameter('cat', $label)
            ->orderBy('l.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** Supprime les lots orphelins (sans événement) et retourne le nombre supprimé */
    public function removeOrphanedLots(): int
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'DELETE l FROM lot l LEFT JOIN evenement_enchere e ON l.evenement_enchere_id = e.id WHERE e.id IS NULL';
        $stmt = $conn->executeQuery($sql);
        return $stmt->rowCount();
    }

    /** Récupère les lots qui ont un événement lié (basique) */
    public function findAllWithValidEvent(): array
    {
        return $this->createQueryBuilder('l')
            ->leftJoin('l.evenementEnchere', 'e')
            ->addSelect('e')
            ->andWhere('e.id IS NOT NULL')
            ->orderBy('l.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
