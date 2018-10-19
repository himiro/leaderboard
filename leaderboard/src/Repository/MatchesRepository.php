<?php

namespace App\Repository;

use App\Entity\Matches;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class Winner{
    const DRAW = 0;
    const WIN_TEAM1 = 1;
    const WIN_TEAM2 = 2;
}

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

    /**
     * @param $id_team
     * @return match[]
     */
    public function getMatchesByTeamId($id_team): array
    {
        $qb = $this->createQueryBuilder('match')
            ->andWhere('match.id_team1 = :id
                OR match.id_team2 = :id')
            ->setParameter('id', $id_team)
            ->getQuery();

        return $qb->execute();
    }

    public function getWinCount($id_team)
    {
        $qb = $this->createQueryBuilder('match')
            ->Where('match.id_team1 = :id
                AND match.winner = :win1')
            ->setParameter('id', $id_team)
            ->setParameter('win1', 2)
            ->getQuery();

        return $qb->execute();
    }
}
