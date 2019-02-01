<?php

namespace App\Repository;

use App\Entity\FoodEstablishment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method FoodEstablishment|null find($id, $lockMode = null, $lockVersion = null)
 * @method FoodEstablishment|null findOneBy(array $criteria, array $orderBy = null)
 * @method FoodEstablishment[]    findAll()
 * @method FoodEstablishment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FoodEstablishmentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FoodEstablishment::class);
    }

    // /**
    //  * @return FoodEstablishment[] Returns an array of FoodEstablishment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FoodEstablishment
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
