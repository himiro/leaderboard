<?php

namespace App\Repository;

use App\Entity\Matches;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Matches|null find($id, $lockMode = null, $lockVersion = null)
 * @method Matches|null findOneBy(array $criteria, array $orderBy = null)
 * @method Matches[]    findAll()
 * @method Matches[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MatchesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Matches::class);
    }

//    /**
//     * @return Matches[] Returns an array of Matches objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Matches
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
