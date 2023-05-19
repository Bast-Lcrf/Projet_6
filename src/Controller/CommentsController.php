<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Form\CommentsType;
use App\Repository\CommentsRepository;
use App\Repository\TricksRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/comments')]
class CommentsController extends AbstractController
{
    /**
     * This controller allow us to add a comment
     * 
     *@isGranted("ROLE_USER")
     * @return void
     */
    #[Route('/new', name: 'app_comments_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        CommentsRepository $commentsRepository,
        TricksRepository $tricksRepository,
        Security $security
    ): Response
    {
        $comment = new Comments();
        // On récupère le pseudo de l'utilisateur
        $currentUserPseudo = $security->getUser()->getPseudo();

        // On récupère les données du formulaire
        if($request->isMethod('post')) {
            $posts = $request->request->all();
            $comment->setComment(htmlspecialchars($posts['comments']));
            // On envoie le pseudo 
            $comment->setPseudo($currentUserPseudo);
            // On envoie l'entité utilisateur
            $comment->setIdUser($security->getUser());

            // On set up la date 
            $dateTime = new \DateTimeImmutable('now');
            $comment->setCreatedAt($dateTime);

            // On récupère le tricks 
            $tricks = $tricksRepository->findOneById([$posts['idTrick']]);
            $comment->setIdTrick($tricks);

            // On sauvegarde dans la BDD
            $commentsRepository->save($comment, true);

            // On flash un message de validation, puis redirige vers le tricks
            $this->addFlash(
                'success',
                'Le commentaire à bien été ajouté'
            );
            return $this->redirectToRoute('app_tricks_show', ['slug' => $tricks->getSlug()], Response::HTTP_SEE_OTHER);
        } else {
            // Une erreur s'est produite
            $this->addFlash(
                'error',
                'Une erreur s\'est produite, merci de bien vouloir réessayer'
            );
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
    }

    #[Route('/{id}', name: 'app_comments_show', methods: ['GET'])]
    public function show(Comments $comment, Security $security): Response
    {
        return $this->render('comments/show.html.twig', [
            'comment' => $comment,
            'users' => $security->getUser()
        ]);
    }

    #[Route('/{id}/edit', name: 'app_comments_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comments $comment, CommentsRepository $commentsRepository): Response
    {
        $form = $this->createForm(CommentsType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentsRepository->save($comment, true);

            return $this->redirectToRoute('app_comments_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('comments/edit.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_comments_delete', methods: ['POST'])]
    public function delete(Request $request, Comments $comment, CommentsRepository $commentsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $commentsRepository->remove($comment, true);
        }

        $this->addFlash(
            'success',
            'Le commentaire a bien été supprimé.'
        );
        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }
}
