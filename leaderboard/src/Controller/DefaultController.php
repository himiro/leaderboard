<?php
/**
 * Created by PhpStorm.
 * User: Mathilde Chabeau
 * Date: 11/10/18
 * Time: 09:36
 */

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function index()
    {
        $team = $this->getDoctrine()
            ->getRepository(Teams::class)
            ->findAll();

        return $this->render('teams/index.html.twig', ['teams' => $team]);
    }
}