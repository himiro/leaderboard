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
        return new Response('
            <html>
                <body>
                    <h1>Voici la page d\'accueil.</h1> Elle doit afficher les équipes avec les éléments suivants:
                    - nom,
                    - nombre de points,
                    - nombre de victoires,
                    - nombre de défaites
                    </h1>
                </body>
            </html>
        ');
    }
}