<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MatchesController extends AbstractController
{

    public function getMatches()
    {
        $match = $this->getDoctrine()
            ->getRepository(Matches::class)
            ->findAll();

        return $this->render('matches/index.html.twig', ['matches' => $match]);
    }


}
