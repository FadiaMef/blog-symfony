<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TestController extends AbstractController
{
    /**
     * @Route("/test", name="app_test")
     */

    public function index(): Response
    {
        
        $prenom = "Fadia"; 
        $nom = "Meftah";
        $tab = [ 
            'personne1' => [ 
                'nom'=> 'Snow',
                'prenom'=> 'Jon',
            ],
            'personne2' => [ 
                'nom'=> 'Tata',
                'prenom'=> 'Toto',
            ],
            'personne3' => [ 
                'nom'=> 'Moi',
                'prenom'=> 'moi',
            ]
            ];
        
        return $this->render("test.html.twig", [ 
            'prenom' => $prenom, #'cle'/'index' => $valeur# on accède à la valeur de $prenom en passant par la variable prenom dans le tableau}
            'nom' => $nom,
            'personnes' => $tab
        ]);
    }

/**
 * @Route("/test-home", name="app_test-home")
 */

    public function testHome(){
        $articles = [
                'titre'=> 'Mon article',
                'dateDeCreation'=> '05-01-2023',
                'dateDeModification'=>'10-01-2023',
                'auteur'=> 'Fadia Meftah',
                'contenu'=> 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
        ];

        return $this->render("home/index.html.twig", [ 
            'articles' => $articles
        ]);
    }
}
