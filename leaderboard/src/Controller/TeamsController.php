<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Teams;
use App\Entity\Matches;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

class TeamsController extends AbstractController
{

    /**
     * @Route("/teams", name="teams_getTeams")
     */
    public function getTeams()
    {
        $team = $this->getDoctrine()
            ->getRepository(Teams::class)
            ->findAll();

        return $this->render('teams/index.html.twig', ['teams' => $team]);
    }


    /*
     * TODO
     * Remove addTeam ?
     */

    /**
     * @Route("/teams/add/{name}/{skillMu}/{skillSigma}", name="addTeam")
     */
    public function addTeam($name = "", $skillMu = 0, $skillSigma = 0)
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

    /**
     * @Route("/teams/{id_team}", name="teams_getTeamById")
     */
    public function getTeamById($id_team)
    {
        $team = $this->getDoctrine()
            ->getRepository(Teams::class)
            ->find($id_team);

        if (!$team) {
            throw $this->createNotFoundException(
                'No product found for id ' . $id_team
            );
        }

        return new Response('Check out this great team: ' . $team->getName());
    }

}
