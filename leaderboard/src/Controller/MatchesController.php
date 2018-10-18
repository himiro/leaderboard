<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Matches;
use App\Entity\Teams;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class MatchesController extends AbstractController
{

    /**
     * @Route("/matches", name="matches_getMatches")
     */

    public function getMatches()
    {
        $match = $this->getDoctrine()
            ->getRepository(Matches::class)
            ->findAll();

        return $this->render('matches/index.html.twig', ['matches' => $match]);
    }

    /*
 * TODO
 * Remove addMatches ?
 */
    /**
     * @Route("/matches/add/{id_team1}/{id_team2}/{start}/{end}/{winner}", name="matches_addMatch")
     */
    public function addMatch($id_team1, $id_team2, $start, $end, $winner)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $match = new Matches();
        $match->setIdTeam1($id_team1);
        $match->setIdTeam2($id_team2);
        $date = new \DateTime($start);
        $match->setStart($date);
        $date = new \DateTime($end);
        $match->setEnd($date);
        $match->setWinner($winner);

        $entityManager->persist($match);

        $entityManager->flush();

        return new Response('Saved new match with id ' . $match->getId());
    }

    /**
     * @Route("/matches/{id_team}", name="matches_getTeamsAndMatches")
     */
    public function getTeamsAndMatches($id_team)
    {


        $match = $this->getDoctrine()
            ->getRepository(Matches::class)
            ->getMatchesByTeamId($id_team);

        $team = $this->getDoctrine()
            ->getRepository(Teams::class)
            ->find($id_team);

        /*
         * Mu : 25
         * Sigma : 25/4
         * Beta : 4.166666666666667
         * Tau : 0.08333333333333334
         * Draw proba : 0.1
         * Epsilon : 50
         */

        /*calcMu();
        calcSigma();*/

        return $this->render('matches/matches.html.twig', ['matches' => $match, 'teams' => $team]);

    }

    public function calcMu()
    {
        return ;
    }

    public function calcSigma()
    {
        return ;
    }

}
