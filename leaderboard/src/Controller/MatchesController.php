<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MatchesController extends AbstractController
{
    /**
     * @Route("/matches", name="matches")
     */
    public function index()
    {
        return $this->render('matches/index.html.twig', [
            'controller_name' => 'MatchesController',
        ]);
    }
}
