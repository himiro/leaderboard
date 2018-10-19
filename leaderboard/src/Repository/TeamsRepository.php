<?php

namespace App\Repository;

use App\Entity\Teams;
use App\Entity\Matches;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Teams|null find($id, $lockMode = null, $lockVersion = null)
 * @method Teams|null findOneBy(array $criteria, array $orderBy = null)
 * @method Teams[]    findAll()
 * @method Teams[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TeamsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Teams::class);
    }

    /**
     * @return team[]
     */
    public function getTeamsOrder(): array
    {
        $qb = $this->createQueryBuilder('team')
            ->orderBy('team.skill_mu', 'ASC')
            ->orderBy('team.skill_sigma', 'DESC')
            ->getQuery()
            ->getArrayResult();

        return $qb;
    }

    /**
     * @param $id_team
     * @return team[]
     */
    public function getTeamName($id_team)
    {
        $qb = $this->createQueryBuilder('team')
            ->andWhere('team.id = :id')
            ->setParameter('id', $id_team)
            ->Select('team.name')
            ->getQuery()
            ->getSingleScalarResult();

        return $qb;
    }

    /**
     * @param $id_team
     * @return team[]
     */
    public function getMuById($id_team)
    {
        $qb = $this->createQueryBuilder('team')
            ->andWhere('team.id = :id')
            ->setParameter('id', $id_team)
            ->Select('team.skill_mu')
            ->getQuery()
            ->getSingleScalarResult();

        return $qb;
    }

    /**
     * @param $id_team
     * @return team[]
     */
    public function getSigmaById($id_team)
    {
        $qb = $this->createQueryBuilder('team')
            ->andWhere('team.id = :id')
            ->setParameter('id', $id_team)
            ->Select('team.skill_sigma')
            ->getQuery()
            ->getSingleScalarResult();

        return $qb;
    }
}
