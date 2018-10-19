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
