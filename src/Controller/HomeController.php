<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Tricks;
use App\Repository\ImagesRepository;
use App\Repository\TricksRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(
        TricksRepository $tricksRepository,
        ImagesRepository $imagesRepository,
        Security $security
        ): Response
    {
        return $this->render('home/index.html.twig', [
            'tricks' => $tricksRepository->findAll(),
            'images' => $imagesRepository->findAll(),
            'users' => $security->getUser()
        ]);
    }
}
