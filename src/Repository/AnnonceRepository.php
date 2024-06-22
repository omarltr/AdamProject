<?php

namespace App\Repository;

use App\Entity\Annonce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<Annonce>
 *
 * @method Annonce|null find($id, $lockMode = null, $lockVersion = null)
 * @method Annonce|null findOneBy(array $criteria, array $orderBy = null)
 * @method Annonce[]    findAll()
 * @method Annonce[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnonceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annonce::class);
    }

    public function findMonthlyCounts(): array
{
    $qb = $this->createQueryBuilder('c');
    
    $qb->select("DATE_FORMAT(a.date_creation, '%Y-%m') AS moisAnnonce")
       ->addSelect('COUNT(a.id) as nombreAnnonces')
       ->leftJoin('c.annonces', 'a')
       ->groupBy('moisAnnonce')
       ->orderBy('moisAnnonce', 'ASC');
    
    return $qb->getQuery()->getResult();
}

    public function findBySearchTerm($term)
    {
        return $this->createQueryBuilder('u')
            ->where('u.nom LIKE :term OR u.message LIKE :term OR u.description LIKE :term ')
            ->setParameter('term', '%' . $term . '%')
            ->getQuery()
            ->getResult();
    }

    public function findByUser($value): array
   {
       return $this->createQueryBuilder('a')
           ->andWhere('a.user = :val')
           ->setParameter('val', $value)
           ->orderBy('a.date_creation', 'ASC')
           ->getQuery()
           ->getResult()
       ;
   }

//    /**
//     * @return Annonce[] Returns an array of Annonce objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Annonce
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
