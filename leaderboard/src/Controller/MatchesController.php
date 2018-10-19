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

    ///**
     //* @Route("/matches/autoadd/{nb}, name="matches_addnMatch")
     //*/
    /*public function addnMatch($nb)
    {
        $entityManager = $this->getDoctrine()->getManager();


        for ($i = 0; $i < $nb; $i++) {
            $match = new Matches();
            $match->setIdTeam1(rand() % 21 + 1);
            $match->setIdTeam2(rand() % 21 + 1);
            $start = rand(1262055681, 1262055681);
            $date = new \DateTime($start);
            $match->setStart($date);
            $end = $start + (rand() % 4000);
            $date = new \DateTime($end);
            $match->setEnd($date);
            $match->setWinner(rand() % 3);

            $entityManager->persist($match);
            $entityManager->flush();
        }
        return new Response('Saved new match with id ' . $match->getId());
    }*/

    /**
     * @Route("/matches/{id_team}", name="matches_getRanking")
     */
    public function getRanking($id_team)
    {


        $match = $this->getDoctrine()
            ->getRepository(Matches::class)
            ->getMatchesByTeamId($id_team);

        $team = $this->getDoctrine()
            ->getRepository(Teams::class)
            ->find($id_team);

        $name = [];
        foreach ($match as $m) {
            if ($m['id_team1'] != $id_team) {
            $tmp = $this->getDoctrine()
                ->getRepository(Teams::class)
                ->getTeamName($m['id_team1']);
                array_push($name, $tmp);
            }
            else{
                $tmp = $this->getDoctrine()
                    ->getRepository(Teams::class)
                    ->getTeamName($m['id_team2']);
                array_push($name, $tmp);
            }
        }

        /*
         * Mu : 25
         * Sigma : 25/4
         * Beta : 4.166666666666667
         * Tau : 0.08333333333333334
         * Draw proba : 0.1
         * Epsilon : 50
         */

        $resultMatches = [];
        (int)$winPoints = $this->getDoctrine()
            ->getRepository(Matches::class)
            ->getWinCount($id_team);

        (int)$loosePoints = $this->getDoctrine()
            ->getRepository(Matches::class)
            ->getLooseCount($id_team);

        $points = $this->calcPoints($winPoints, $loosePoints);

        array_push($resultMatches, $winPoints);
        array_push($resultMatches, $loosePoints);
        array_push($resultMatches, $points);

        $this->calcMu();
        $this->calcSigma();

        return $this->render('matches/matches.html.twig', ['matches' => $match, 'teams' => $team, 'name' => $name, 'resultMatches' => $resultMatches]);

    }

    public function calcPoints($winPoints, $loosePoints)
    {
        return ($winPoints - $loosePoints);
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
