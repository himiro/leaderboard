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
            ->findAll();

        return $this->render('teams/index.html.twig', ['teams' => $team]);
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

        $points = $this->calcPoints($winPoints, $loosePoints);

        array_push($resultMatches, $winPoints);
        array_push($resultMatches, $loosePoints);
        array_push($resultMatches, $points);

        return $resultMatches;
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
