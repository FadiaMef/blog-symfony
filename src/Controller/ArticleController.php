<?php

namespace App\Controller;

use DateTime;
use App\Entity\Article;
use App\Form\ArticleType;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\AuteurRepository;
use App\Repository\ArticleRepository;
use App\Repository\CategorieRepository;
use App\Repository\CommentaireRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ArticleController extends AbstractController
{
    #[Route('/articles', name: 'app_articles')]
    public function showAll(ArticleRepository $repo, CategorieRepository $repoCat): Response
    {
        // on récupere les articles en passant par un objet de l'ArticleRepository et en utilisant la methode findAll()
        $articles = $repo->findAll();
        $categories= $repoCat->findAll();
        
        
        // dd() est une fonction de debogage (équivalent d'un var_dump() et die() en même temps)
        //dd($articles);
        
        return $this->render('article/allArticles.html.twig', [
                'articles' => $articles,
                'categories' => $categories,
                
        ]);
    }

    // <\d+> est une regex qui permet de dire que l'information qu'on met dans l'id doit être un entier de 1 à l'infini. sans quoi cette route pourrait être confondu avec d'autres. ex: la route suivante /article_add, le add aurait été pris pour un id et donc intercepté avant d'y arrivé 

    #[Route('/article_{id<\d+>}', name: 'app_article')]
    public function show($id, ArticleRepository $repo, CommentaireRepository $repoCom, Request $request){
        
        $article = $repo->find($id);

        $commentaire = new Commentaire();

        $form = $this->createForm(CommentaireType::class, $commentaire);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $commentaire->setDateDeCreation(new DateTime("now"));
            $commentaire->setArticle($article);

            $repoCom->save($commentaire, 1);
            $this->addFlash("success", "Le commentaire a bien été ajouté !");

            return $this->redirectToRoute("app_article", ['id' => $id]);//['id' => $article->getId()] autre possibilité
        }
        //dd($article);

        return $this->render('article/oneArticle.html.twig' , [
            'article' => $article,
            'formComment' => $form->createView()

        ]);

    }


    #[Route('/article_add', name: 'app_article_add')]
    public function add(Request $request, ArticleRepository $repo, SluggerInterface $slugger)
    {
        //on crée un objet de la class Article
        $article = new Article();

        //on crée le formulaire en liant le ArticleType à l'objet $article
        $form = $this->createForm(ArticleType::class, $article);

        // on donne accés aux donnée post du formulaire
        $form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid())
        {

            //On récupère l'image depuis le formulaire:
            $file = $form-> get('imageForm')->getData(); //$FILE est l'image physique
            // dd($file);

            if($file)
            {   //Ici on crée un nom avec lequel on renomme notre image lors de l'enregistrement dans notre dossier et dan sla bdd
                //guessExtension récupère l'extension du fichier (jpeg, jpg, png...)
                $fileName = $slugger->slug($article->getTitre()) . uniqid() . '.' . $file->guessExtension();
                // dd($fileName);

                try{
                    //On cherche à enregistrer l'image du formulaire dans notre dossier paramétré dans service.yaml "articles_images sous le nom qu'on a crée "$fileName"
                    $file->move($this->getParameter('article_images'), $fileName);//getParameter récupere le parametre qu'on vient d'ajouter dans le services.yaml pour envoyer nos photos dans le dossier
                }catch(FileException $e)
                {
                        //  on gére les exceptions durant l'upload

                }
                $article->setImage($fileName);
                
            }

            $article->setDateDeCreation(new DateTime("now"));

            // la methode add() me permet de faire un persist puis un flush en ajoutant le 2éme paramétre 1 ou true. elle se trouve dans le repository ArticleRepository
            $repo->add($article, 1);

            $this->addFlash("success", "L'article a bien été ajouté !");

            // aprés avoir ajouté l'article en bdd, on redirige vers la page de tous les articles
            return $this->redirectToRoute("app_articles");

        }

        return $this->render('article/formArticle.html.twig', [
            //on crée la vue du formulaire pour l'afficher dans le template
            'formArticle' => $form->createView()
        ]);
    }



    #[Route('/article_update_{id<\d+>}', name: 'app_article_update')]
    public function update($id, Request $request, ArticleRepository $repo, SluggerInterface $slugger)
    {
         //on recupére l'article dont l'id est celui passé en paramétre de la route, qui est automatiquement recupéré dans le $id de la fonction, pour pouvoir le modifier 
        $article = $repo->find($id);

        //on crée le formulaire en liant le ArticleType à l'objet $article
        $form = $this->createForm(ArticleType::class, $article);

        // on donne accés aux données post du formulaire
        $form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid())
        {
            //On récupère l'image depuis le formulaire:
                $file = $form-> get('imageForm')->getData(); //$FILE est l'image physique
                // dd($file);
    
                if($file)
                {   //Ici on crée un nom avec lequel on renomme notre image lors de l'enregistrement dans notre dossier et dan sla bdd
                    //guessExtension récupère l'extension du fichier (jpeg, jpg, png...)
                    $fileName = $slugger->slug($article->getTitre()) . uniqid() . '.' . $file->guessExtension();
                    // dd($fileName);
    
                    try{
                        //On cherche à enregistrer l'image du formulaire dans notre dossier paramétré dans service.yaml "articles_images sous le nom qu'on a crée "$fileName"
                        $file->move($this->getParameter('article_images'), $fileName);//getParameter récupere le parametre qu'on vient d'ajouter dans le services.yaml pour envoyer nos photos dans le dossier
                    }catch(FileException $e)
                    {
                            //  on gére les exceptions durant l'upload
    
                    }
                    $article->setImage($fileName);
                    
                }
            $article->setDateDeModification(new DateTime("now"));

            // la methode add() me permet de faire un persist puis un flush en ajoutant le 2éme paramétre 1 ou true. elle se trouve dans le repository ArticleRepository
            $repo->add($article, 1);
            $this->addFlash("success", "L'article' a bien été modifié !");

            // aprés avoir ajouté l'article en bdd, on redirige vers la page de tous les articles
            return $this->redirectToRoute("app_article", ['id' => $id]);

        }

        return $this->render('article/formArticle.html.twig', [
            //on crée la vue du formulaire pour l'afficher dans le template
            'formArticle' => $form->createView()
        ]);
    }

    #[Route('/article_delete_{id<\d+>}', name: 'app_article_delete')]
    public function delete($id, ArticleRepository $repo)
    {
            $article = $repo->find($id);
    // la methode rmeove() me permet de faire un remove puis un flush en ajoutant le 2éme paramétre 1 ou true. elle se trouve dans le repository ArticleRepository
            $repo->remove($article,1);
            $this->addFlash("success", "L'article' a bien été supprimé !");

            return $this->redirectToRoute("app_articles");
    }


}
