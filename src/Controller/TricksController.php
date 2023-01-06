<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Tricks;
use App\Entity\Videos;
use App\Form\TricksFormType\EditTrickType;
use App\Repository\ImagesRepository;
use App\Repository\TricksRepository;
use App\Repository\VideosRepository;
use App\Repository\CommentsRepository;
use App\Form\TricksFormType\NewTrickType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Func;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/tricks')]
class TricksController extends AbstractController
{       
    /**
     * This controller allow us to create a new trick
     * with restricted role as ROLE_USER
     *
     * @param  mixed $request
     * @param  mixed $tricksRepository
     * @param  mixed $security
     * 
     * @isGranted("ROLE_USER")
     * @return Response
     */
    #[Route('/new', name: 'app_tricks_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TricksRepository $tricksRepository, Security $security): Response
    {
        $trick = new Tricks();
        $form = $this->createForm(NewTrickType::class, $trick);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère les données du champ video
            $video = $form->get('video')->getData();

            // on vérifie elle n'est pas vide
            if(!empty($video)) {
                // On vérifie que ce soit bien un lien youtube
                if(str_contains($video, 'www.youtube.com')) {
                    // On modifie le lien pour pouvoir le lire plus tard
                    $newLink = str_replace('/watch?v=', '/embed/', $video);

                    // On le stocke dans la base de données
                    $videoEntity = new Videos();
                    $videoEntity->setName($newLink);
                    $trick->addVideo($videoEntity);
                } else {
                    $this->addFlash(
                        'error',
                        'Le tricks n’a pas été ajouté, le lien video ne correspond pas !'
                    );
                    return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
                }
            }

            // On récupère les images envoyés depuis le formulaire
            $images = $form->get('images')->getData();

            // On boucle sur les images
            foreach($images as $image) {
                // On génère un nouveau nom de fichier
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();

                // On copie le fichier dans le dossier imagesTricksUploads
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );

                // On stocke l'image dans la base de données (son nom)
                $img = new Images();
                $img->setName($fichier);
                $trick->addImage($img);
            }

            // On slug le nom pour le mettre en BDD
            $slugger = new AsciiSlugger();
            $slug = $slugger->slug($form->get('name')->getdata());
            $trick->setSlug($slug);

            $tricksRepository->save($trick, true);

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tricks/new.html.twig', [
            'trick' => $trick,
            'formNewTrick' => $form,
            'users' => $security->getUser()
        ]);
    }

    /**
     * This controller allow us to see the tricks in detail
     *
     * @return void
     */
    #[Route('/{slug}', name: 'app_tricks_show', methods: ['GET'])]
    public function show(
        Request $request,
        Tricks $trick,
        Security $security,
        CommentsRepository $commentsRepository,
        ImagesRepository $imagesRepository,
        VideosRepository $videosRepository
        ): Response
    {
        //On va chercher le numéro de page dans l'url
        $page = $request->query->getInt('page', 1);

        // On va chercher les commentaires du tricks
        $comments = $commentsRepository->getPaginatedComment($trick->getId(), $page, 5);

        return $this->render('tricks/show.html.twig', [
            'trick' => $trick,
            'videos' => $videosRepository->findByTrick($trick->getId()),
            'images' => $imagesRepository->findByIdTrick($trick->getId()),
            'comments' => $comments,
            'users' => $security->getUser()
        ]);
    }

    /**
     * This controller allow us to edit the tricks by its id
     * With restricted role as ROLE_USER
     *
     * @param  mixed $request
     * @param  mixed $trick
     * @param  mixed $tricksRepository
     * @param  mixed $security
     * 
     * @isGranted("ROLE_USER")
     * @return Response
     */
    #[Route('/{id}/edit', name: 'app_tricks_edit', methods: ['GET', 'POST'])] 
    public function edit(
        Request $request,
        Tricks $trick,
        TricksRepository $tricksRepository,
        Security $security,
        VideosRepository $videosRepository,
        ImagesRepository $imagesRepository
    ): Response
    {
        $form = $this->createForm(EditTrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On fait un update de la date 
            $dateTime = new \DateTimeImmutable("Europe/Paris");
            $trick->setUpdatedAt($dateTime);

            // On récupère le lien de la video
            $video = $form->get('video')->getData();

            // on vérifie si elle existe
            if(!empty($video)) {
                // On vérifie que ce soit bien un lien Youtube
                if(str_contains($video, 'www.youtube.com')) {
                    // On modifie le lien pour pouvoir le lire plus tard
                    $newLink = str_replace('/watch?v=', '/embed/', $video);

                    // On stocke dans la base de données
                    $videoEntity = new Videos();
                    $videoEntity->setName($newLink);
                    $trick->addVideo($videoEntity);
                } else {
                    $this->addFlash(
                        'error',
                        'Le tricks n’a pas été modifié, le lien video ne correspond pas !'
                    );
                    return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
                }
            }

            // On récupère les images envoyés
            $images = $form->get('images')->getData();

            // On boucle sur les images
            foreach($images as $image) {
                // On génère un nouveau nom de fichier
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();

                // On copie le fichier dans le dossier imagesTricksUploads
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );

                // On stocke l'image dans la base de données (son nom)
                $img = new Images();
                $img->setName($fichier);
                $trick->addImage($img);
            }           

            $tricksRepository->save($trick, true);

            return $this->redirectToRoute('app_tricks_show', ['slug' => $trick->getSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tricks/edit.html.twig', [
            'trick' => $trick,
            'formEditTrick' => $form,
            'users' => $security->getUser(),
            'videos' => $videosRepository->findByTrick($trick->getId()),
            'images' => $imagesRepository->findByIdTrick($trick->getId()),
        ]);
    }

        
    /**
     * This controller requires a second validation before complete deletion
     * With restricted role as ROLE_USER
     *
     * @param  mixed $tricks
     * @param  mixed $security
     * 
     * @isGranted("ROLE_USER")
     * @return void
     */
    #[Route('/before-delete/{slug}', name: 'tricks.before.delete', methods: ['GET', 'POST'])]
    public function beforeDelete(Tricks $tricks, Security $security)
    {
        return $this->render('tricks/tricksDelete.html.twig', [
            'trick' => $tricks,
            'users' => $security->getUser()
        ]);
    }

       
    /**
     * This controller allow us to delete a tricks by its id
     * and removes the linked fields
     *
     * @param  mixed $request
     * @param  mixed $trick
     * @param  mixed $tricksRepository
     * 
     * @return Response
     */
    #[Route('/{id}', name: 'app_tricks_delete', methods: ['POST'])] 
    public function delete(
        Request $request,
        Tricks $trick,
        TricksRepository $tricksRepository,
        ImagesRepository $imagesRepository
    ): Response
    {
        if ($this->isCsrfTokenValid('delete'.$trick->getId(), $request->request->get('_token'))) {
            // On récupère les images lié au tricks
            $image = $imagesRepository->findByIdTrick($trick->getId());
            
            // On boucle pour supprimer toutes les images liées
            foreach($image as $images) {
                unlink($this->getParameter('images_directory') . '/' . $images->getName());
            }

            $tricksRepository->remove($trick, true);
        }

        $this->addFlash(
            'success',
            'Le Tricks a bien été supprimé.'
        );
        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }

   
    /**
     * This controller allow us to delete images tricks with Ajax
     *
     * @param  mixed $image
     * @param  mixed $request
     * @param  mixed $manager
     * @return void
     */
    #[Route('/supprime/image/{id}', name: 'app_delete_image', methods: ['DELETE'])]
    public function deleteImage(Images $image, Request $request, EntityManagerInterface $manager)
    {
        $data = json_decode($request->getContent(), true);

        // On vérifie si le token est valide
        if($this->isCsrfTokenValid('delete' . $image->getId(), $data['_token'])) {
            // On récupère le nom de l'image
            $nom = $image->getName();
            // On supprime le fichier
            unlink($this->getParameter('images_directory') . '/' . $nom);

            // On supprime l'entrée de la base
            $manager->remove($image);
            $manager->flush();

            // On repond en json
            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }
        
    /**
     * This controller allow us to delete videos with Ajax
     *
     * @param  mixed $video
     * @param  mixed $request
     * @param  mixed $manager
     * @return void
     */
    #[Route('/supprime/video/{id}', name: 'app_video_delete', methods: ['DELETE'])] 
    public function deleteVideos(Videos $video, Request $request, EntityManagerInterface $manager)
    {
        $data = json_decode($request->getContent(), true);

        //On vérifie si le token est valide
        if($this->isCsrfTokenValid('delete' . $video->getId(), $data['_token'])) {
            // On supprime l'entrée de la base
            $manager->remove($video);
            $manager->flush();

            // On repond en json
            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }
}
