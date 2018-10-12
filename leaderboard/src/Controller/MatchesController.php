<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Matches;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Validator\Constraints\DateTime;

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
     * @Route("/matches/{id_team}", name="matches_getMatchesByTeamId")
     */
    public function getMatchesByTeamId($id_team)
    {
        $match = $this->getDoctrine()
            ->getRepository(Matches::class)
            ->findBy(
            ['id_team1' => $id_team]
            );

        if (!$match) {
            throw $this->createNotFoundException(
                'No product found for id ' . $id_team
            );
        }
        $response = new JsonResponse();
        $response->setData($match);
        return $response;
    }
}
