<?php

namespace App\Repository;

use App\Entity\Taches;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Taches>
 *
 * @method Taches|null find($id, $lockMode = null, $lockVersion = null)
 * @method Taches|null findOneBy(array $criteria, array $orderBy = null)
 * @method Taches[]    findAll()
 * @method Taches[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TachesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Taches::class);
    }

    public function countAll(): int
    {
        return $this->createQueryBuilder('t')
            ->select('count(t.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countByStatus(string $status): int
    {
        return $this->createQueryBuilder('t')
            ->select('count(t.id)')
            ->where('t.statut = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getSingleScalarResult();
    }

    // Tri et Filtre
    public function findAllWithSortAndFilter($sort = null, $status = null)
    {
        $qb = $this->createQueryBuilder('t');

        if ($status) {
            $qb->where('t.statut = :status')
                ->setParameter('status', $status);
        }

        // Tri par priorité puis par le champ choisi
        $qb->addSelect('CASE 
        WHEN t.priorite = \'Haute\' THEN 3
        WHEN t.priorite = \'Moyenne\' THEN 2
        WHEN t.priorite = \'Basse\' THEN 1
        ELSE 0
    END AS HIDDEN orderField')
            ->orderBy('orderField', 'DESC');

        if ($sort) {
            $qb->addOrderBy('t.' . $sort);
        }

        // Affiche les priorités avant le tri
        return $qb->getQuery()->getResult();
    }





    //    /**
    //     * @return Taches[] Returns an array of Taches objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Taches
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
