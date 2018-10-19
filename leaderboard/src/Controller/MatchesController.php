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
     * @Route("/team/{id_team}", name="matches_getRanking")
     */
    /*
     * Valeurs par défaut
     * Mu : 25
    * Sigma : 25/4
    * Beta : 4.166666666666667
    * Tau : 0.08333333333333334
    * Draw proba : 0.1
    * Epsilon : 50
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
        $time = [];

        //Get the Loss, Tie and Win from a team
        $resultMatches = [];
        (int)$winPoints = $this->getDoctrine()
            ->getRepository(Matches::class)
            ->getWinCount($id_team);

        (int)$loosePoints = $this->getDoctrine()
            ->getRepository(Matches::class)
            ->getLooseCount($id_team);

        (int)$drawPoints = $this->getDoctrine()
            ->getRepository(Matches::class)
            ->getDrawCount($id_team);

        //Calculate the score
        $points = $this->calcPoints($winPoints, $loosePoints);

        array_push($resultMatches, $winPoints);
        array_push($resultMatches, $loosePoints);
        array_push($resultMatches, $drawPoints);
        array_push($resultMatches, $points);
        foreach ($match as $m) {
            //Get name of opposed teams

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

            //Get time of a match
            $tmp = $m['start']->diff($m['end']);
            array_push($time, $tmp);

            //Recalculate Skill/Rank at each match
            if ($m['winner'] == 1)
            {
                $tmpMu = $this->getDoctrine()
                    ->getRepository(Teams::class)
                    ->getMuById($m['id_team1']);
                $tmpSigma = $this->getDoctrine()
                    ->getRepository(Teams::class)
                    ->getSigmaById($m['id_team1']);

                //Recalculate Sigma at each match and assign it to new sigma

                $tmpSigma += 0.25;
                $this->updateSigma($m['id_team1'], $m['id_team2'], $tmpSigma);
            }
            else if ($m['winner'] == 2)
            {
                $tmpMu = $this->getDoctrine()
                    ->getRepository(Teams::class)
                    ->getMuById($m['id_team2']);
                $tmpSigma = $this->getDoctrine()
                    ->getRepository(Teams::class)
                    ->getSigmaById($m['id_team2']);

                //Recalculate Sigma at each match and assign it to new sigma

                $tmpSigma += 0.25;
                $this->updateSigma($m['id_team1'], $m['id_team2'], $tmpSigma);
            }
        }


        $this->updateMu($m['id_team1'], $m['id_team2'], $tmpSigma, $tmpMu, $points, $m['winner'], $id_team);
        $rank = $this->calcSkill($tmpMu, $tmpSigma);

        return $this->render('matches/matches.html.twig', ['matches' => $match, 'teams' => $team, 'name' => $name, 'resultMatches' => $resultMatches, 'time' => $time, 'rank' => $rank]);

    }

    public function updateSigma($id_team1, $id_team2, $sigma)
    {
        $em = $this->getDoctrine()->getManager();

        $team1 = $em->getRepository(Teams::class)->find($id_team1);
        $team1->setSkillSigma($sigma);

        $team2 = $em->getRepository(Teams::class)->find($id_team2);
        $team2->setSkillSigma($sigma);

        $em->flush();
    }

    public function updateMu($id_team1, $id_team2, $sigma, $mu, $points, $winner, $id_team)
    {
        $em = $this->getDoctrine()->getManager();

        if ($winner == 1 && $id_team1 == $id_team) {

        $team1 = $em->getRepository(Teams::class)->find($id_team1);
        $team1->setSkillMu($mu + ($points / 100) * $sigma);
        $team2 = $em->getRepository(Teams::class)->find($id_team2);
        $team2->setSkillMu($mu - ($points / 100) * $sigma);
        }
        else if ($winner == 2 && $id_team2 == $id_team)
        {
            $team1 = $em->getRepository(Teams::class)->find($id_team1);
            $team1->setSkillMu($mu - ($points / 100) * $sigma);
            $team2 = $em->getRepository(Teams::class)->find($id_team2);
            $team2->setSkillMu($mu + ($points / 100) * $sigma);
        }

        $em->flush();
    }

    public function calcPoints($winPoints, $loosePoints)
    {
        return ($winPoints - $loosePoints);
    }

    public function calcSkill($mu, $sigma)
    {
        return ($mu - 3 * $sigma);
    }

}
