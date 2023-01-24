<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\ArticleRepository;
use App\Repository\CategorieRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

//Creation d'une catégorie : 
class CategorieController extends AbstractController
{
    #[Route('/categorie_add', name: 'app_categorie_add')]

    public function add(Request $request, CategorieRepository $repo, SluggerInterface $slugger): Response
    {
        $categorie = new Categorie();

        $form = $this->createForm(CategorieType::class , $categorie);

        $form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid() )
        {
            //Un slug est une tranformation d'une chaine de caractère qui remplace les caractères spéciaux
            $slug = $slugger->slug($form->get('nom')->getData());
            $categorie->setSlug($slug);
             //$form->get('nom') permet de récupérer la saisie du champs nom du formulaire: ($categorie->getSlug($slug)====>autre solution)
            $repo->save($categorie, 1);
            $this->addFlash("success", "La catégorie a bien été ajouté !");

            return $this->redirectToRoute('app_categories');
        }
        
        return $this->render('categorie/formCategorie.html.twig', [
            'formCategorie' => $form->createView()
        ]);
    }


//Affichage de toutes les catégories : 

    #[Route('/categories', name: 'app_categories')]

    public function showAll(CategorieRepository $repo)
    {
        $categories = $repo->findAll();
        return $this->render('categorie/allCategorie.html.twig', [
            'categories' => $categories
        ]);
    }

//Modification d'une catégorie :     
    #[Route('/categorie_update_{id<\d+>}', name: 'app_categorie_update')]

    public function update($id, Request $request, CategorieRepository $repo, SluggerInterface $slugger)
    {
        $categorie = $repo->find($id);

        $form = $this->createForm(CategorieType::class, $categorie);

        $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid())
        {
            $slug = $slugger->slug($categorie->getNom());
            $categorie->setSlug($slug);

            $repo->save($categorie, 1);
            $this->addFlash("success", "La catégorie a bien été modifié !");

            return $this->redirectToRoute('app_categories');
        }
        return $this->render('categorie/formCategorie.html.twig' , [
            'formCategorie' => $form->createView()
        ]);
    }

//Suppression d'une catégorie :
    #[Route('/categorie_delete_{id<\d+>}', name: 'app_categorie_delete')]

    public function delete($id, CategorieRepository $repo)
    {
        $categorie = $repo->find($id);

        $repo->remove($categorie, 1);
        $this->addFlash("success", "La catégorie a bien été supprimé !");

        return $this->redirectToRoute('app_categories');

    }
    //Méthode pour afficher les articles par catégorie : on utilise le slug
    // Rappel : La methode find s'utulise uniquement pour l'id

    #[Route('/articles_categorie/{slug}', name: 'app_categorie_articles')]
    public function articleByCategorie($slug, CategorieRepository $repo)
    {
        $categorie = $repo->findOneBy(['slug' => $slug]);
        // dd($categorie);

        // grâce à la relation entre Article et Catégorie, je peux récupérer les articles liés à une catégorie en passant par cette dernière
        $articles = $categorie->getArticles();
        return $this->render('article/allArticles.html.twig', [
            'articles' => $articles,
            'categories' => $repo->findAll() 
        ]);

    }

}