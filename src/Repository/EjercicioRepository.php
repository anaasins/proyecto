<?php

namespace App\Repository;

use App\Entity\Ejercicio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ejercicio|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ejercicio|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ejercicio[]    findAll()
 * @method Ejercicio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EjercicioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ejercicio::class);
    }

    // /**
    //  * @return Ejercicio[] Returns an array of Ejercicio objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Ejercicio
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findAllToShow()
    {
        return $this->createQueryBuilder('e')
            ->where('e.revisado = 1')
            ->andWhere('e.aceptado = 1')
            ->andWhere('e.disponible = 1')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findThreeToShow()
    {
        return $this->createQueryBuilder('e')
            ->where('e.revisado = 1')
            ->andWhere('e.aceptado = 1')
            ->andWhere('e.disponible = 1')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult()
        ;
    }
}
