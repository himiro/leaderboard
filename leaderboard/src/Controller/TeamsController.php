<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Teams;
use App\Entity\Matches;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;


class TeamsController extends AbstractController
{

    /**
     * @Route("/", name="teams_getTeamsInfos")
     */
    public function getTeamsInfos()
    {
        $team = $this->getDoctrine()
            ->getRepository(Teams::class)
            ->getTeams();

        $resultMatches = [];
        foreach ($team as $t) {
            array_push($resultMatches, $this->getTeamsResults($t['id']));
            $points = $resultMatches[$t['id'] - 1][3];

            $match = $this->getDoctrine()
                ->getRepository(Matches::class)
                ->getMatchesByTeamId($t['id']);

            foreach ($match as $m) {
                //Recalculate Skill/Rank at each match
                $tmpMu = 25;
                $tmpSigma = 25 / 3;

                //Recalculate Sigma at each match and assign it to new sigma
                $tmpSigma += 0.25;
                $this->updateSigma($m['id_team1'], $m['id_team2'], $tmpSigma);

                $this->updateMu($m['id_team1'], $m['id_team2'], $tmpSigma, $tmpMu, $points, $m['winner'], $t['id']);
                $rank = $this->calcSkill($tmpMu, $tmpSigma);
            }
        }
        $team = $this->getDoctrine()
            ->getRepository(Teams::class)
            ->getTeamsOrder();

        $resultMatches = [];
        foreach ($team as $t) {
            array_push($resultMatches, $this->getTeamsResults($t['id']));
        }
        return $this->render('teams/index.html.twig', ['teams' => $team, 'resultMatches' => $resultMatches]);
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

    public function getTeamsResults($id_team)
    {
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

        $m = new MatchesController();

        $points = $m->calcPoints($winPoints, $loosePoints);

        array_push($resultMatches, $winPoints);
        array_push($resultMatches, $loosePoints);
        array_push($resultMatches, $drawPoints);
        array_push($resultMatches, $points);

        return $resultMatches;
    }

    public function calcSkill($mu, $sigma)
    {
        // Real formula mu - 3 * sigma
        return ($sigma - $mu);
    }

    /*
     * TODO
     * Remove addTeam ?
     */

    /**
     * @Route("/teams/add/{name}/{skillMu}/{skillSigma}", name="addTeam")
     */
    public function addTeam($name = "", $skillMu = 25, $skillSigma = 25/3)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $team = new Teams();
        $team->setName($name);
        $team->setSkillMu($skillMu);
        $team->setSkillSigma($skillSigma);

        $entityManager->persist($team);

        $entityManager->flush();

        return new Response('Saved new team with id ' . $team->getId());
    }

}
