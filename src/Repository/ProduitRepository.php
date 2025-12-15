<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    public function SearchEngine(String $query){
          return $this->createQueryBuilder('p')
          ->where('p.nom LIKE :query')
          ->orWhere('p.description LIKE :query')
          ->setParameter('query','%'.$query.'%')
          ->getQuery()
          ->getResult();
    }
        /**
         * @return Produit[] Returns an array of Produit objects
         */
        public function findByIdUp($value): array
        {
            return $this->createQueryBuilder('c')
                ->andWhere('c.id > :val')
                ->setParameter('val', $value)
                ->orderBy('c.id', 'DESC')
                //->setMaxResults(10)
                ->getQuery()
                ->getResult()
            ;
        }

    //    public function findOneBySomeField($value): ?Produit
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
