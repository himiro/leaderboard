<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Teams;
use Symfony\Component\HttpFoundation\Response;

class TeamsController extends AbstractController
{
    /**
     * @Route("/teams", name="teams")
     */
    public function index($name, $SkillMu, $SkillSigma)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $team = new Teams();
        $team->setName($name);
        $team->setSkillMu($SkillMu);
        $team->setSkillSigma($SkillSigma);

        $entityManager->persist($team);

        $entityManager->flush();

        return new Response('Saved new team with id '.$team->getId());

        /*return $this->render('teams/index.html.twig', [
            'controller_name' => 'TeamsController',
        ]);*/
    }

    /**
     * @Route("/product/{id}", name="product_show")
     */
    public function getTeamById($id_team)
    {
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($id_team);

        if (!$product) {
            throw $this->createNotFoundException(
                'No product found for id '.$id_team
            );
        }

        return new Response('Check out this great product: '.$product->getName());

        // or render a template
        // in the template, print things with {{ product.name }}
        // return $this->render('product/show.html.twig', ['product' => $product]);
    }
}
