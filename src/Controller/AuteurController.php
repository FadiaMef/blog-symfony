<?php

namespace App\Controller;

use App\Entity\Auteur;
use App\Form\AuteurType;
use App\Repository\AuteurRepository;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AuteurController extends AbstractController
{
    #[Route('/auteur_add', name: 'app_auteur_add')]
    public function add(Request $request, AuteurRepository $repo, SluggerInterface $slugger): Response
    {
        $auteur = new Auteur();

        $form = $this->createForm(AuteurType::class , $auteur);

        $form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid() )
        {
            //On récupère l'image depuis le formulaire:
                $file = $form-> get('imageForm')->getData(); //$FILE est l'image physique
                // dd($file);
    
                if($file)
                {   //Ici on crée un nom avec lequel on renomme notre image lors de l'enregistrement dans notre dossier et dan sla bdd
                    //guessExtension récupère l'extension du fichier (jpeg, jpg, png...)
                    $fileName = $slugger->slug($auteur->getFullName()) . uniqid() . '.' . $file->guessExtension();
                    // dd($fileName);
    
                    try{
                        //On cherche à enregistrer l'image du formulaire dans notre dossier paramétré dans service.yaml "articles_images sous le nom qu'on a crée "$fileName"
                        $file->move($this->getParameter('auteur_images'), $fileName);//getParameter récupere le parametre qu'on vient d'ajouter dans le services.yaml pour envoyer nos photos dans le dossier
                    }catch(FileException $e)
                    {
                            //  on gére les exceptions durant l'upload
    
                    }
                    $auteur->setImage($fileName);
                    
                }
            $repo->add($auteur, 1);
            $this->addFlash("success", "L'auteur a bien été ajouté !");

            return $this->redirectToRoute('app_auteurs');
        }

        return $this->render('auteur/formulaire.html.twig', [
                'formAuteur' => $form->createView()

        ]);
    }


    #[Route('/auteurs', name: 'app_auteurs')]
    public function showAll(AuteurRepository $repo)
    {
        $auteurs = $repo->findAll();
        

        return $this->render('auteur/allAuteur.html.twig', [
            'auteurs' => $auteurs,
            // 
        ]);
    }

    #[Route('/auteur_{id<\d+>}', name: 'app_auteur')]
    public function show($id, AuteurRepository $repo, ArticleRepository $repoArt )
    {
        $auteur = $repo->find($id);
        $articles = $repoArt->findAll();

        return $this->render('auteur/oneAuteur.html.twig', [
            'auteur' => $auteur,
            'articles' => $articles
        ] );
    }



    #[Route('/auteur_update_{id<\d+>}', name: 'app_auteur_update')]
    public function update($id, Request $request, AuteurRepository $repo, SluggerInterface $slugger)
    {
        $auteur = $repo->find($id);

        $form = $this->createForm(AuteurType::class, $auteur);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $file = $form-> get('imageForm')->getData(); //$FILE est l'image physique
                // dd($file);
                if($file)
                {   //Ici on crée un nom avec lequel on renomme notre image lors de l'enregistrement dans notre dossier et dan sla bdd
                    //guessExtension récupère l'extension du fichier (jpeg, jpg, png...)
                    $fileName = $slugger->slug($auteur->getFullName()) . uniqid() . '.' . $file->guessExtension();
                    // dd($fileName);
    
                    try{
                        //On cherche à enregistrer l'image du formulaire dans notre dossier paramétré dans service.yaml "articles_images sous le nom qu'on a crée "$fileName"
                        $file->move($this->getParameter('auteur_images'), $fileName);//getParameter récupere le parametre qu'on vient d'ajouter dans le services.yaml pour envoyer nos photos dans le dossier
                    }catch(FileException $e)
                    {
                            //  on gére les exceptions durant l'upload
    
                    }
                    $auteur->setImage($fileName);
                    
                }
            $repo->add($auteur, 1);
            $this->addFlash("success", "L'auteur a bien été modifié !");

            return $this->redirectToRoute('app_auteurs');
        }

        return $this->render('auteur/formulaire.html.twig' , [
            'formAuteur' => $form->createView()
        ]);
    }


    #[Route('/auteur_delete_{id<\d+>}', name: 'app_auteur_delete')]
    public function delete($id, AuteurRepository $repo){

        $auteur = $repo->find($id);

        $repo->remove($auteur, 1);
        $this->addFlash("success", "L'article a bien été supprimé !");

        return $this->redirectToRoute('app_auteurs');
    }




}
