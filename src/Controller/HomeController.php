<?php

namespace App\Controller;

use App\Repository\SortiesRepository;
use App\Repository\VillesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(SortiesRepository $sortiesRepository, VillesRepository $villesRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'sorties' => $sortiesRepository->findAll(),
            'villes' => $villesRepository->findAll(),

        ]);
    }#[Route('/administration', name: 'app_admin')]
    public function administration(): Response
    {
        return $this->render('participant/admin.html.twig');
    }
}
