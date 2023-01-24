<?php

namespace App\Controller;

use DateTime;
use App\Form\CommentaireType;
use App\Repository\CommentaireRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentaireController extends AbstractController
{
    //Modifier un commentaire ----(la création du commentaire dans articleController)

    #[Route('/commentaire_update_{id<\d+>}', name: 'app_commentaire_update')]
    public function update($id,CommentaireRepository $repo, Request $request): Response
    {
        $commentaire = $repo->find($id);

        $form = $this->createForm(CommentaireType::class, $commentaire);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $commentaire->setDateDeModification(new DateTime("now"));

            $repo->save($commentaire, 1);
            $this->addFlash("success", "Le commentaire a bien été modifié !");

            return $this->redirectToRoute ("app_article", ['id' => $commentaire->getArticle()->getId()]);
        }

        return $this->render('article/oneArticle.html.twig', [
            'formComment' => $form->createView(),
            'article' => $commentaire->getArticle(),
            // 'commentaire' => $commentaire->getArticle() ->getCommentaires()
        ]);
    }

//Supprimer un commentaire:
#[Route('/commentaire_delete_{id<\d+>}', name: 'app_commentaire_delete')]
    public function delete($id,CommentaireRepository $repo)
    {
        $commentaire = $repo->find($id);
        $article = $commentaire->getArticle();

        $repo->remove($commentaire, 1);
        $this->addFlash("success", "Le commentaire a bien été supprimé !");

        return $this->redirectToRoute('app_article', ['id' => $article->getId()]);

    }

}
