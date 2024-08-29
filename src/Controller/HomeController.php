<?php

namespace App\Controller;

use App\Repository\SortiesRepository;
use App\Repository\VillesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="redirect_to_home")
     */
    public function redirectToHome(): RedirectResponse
    {
        // Redirige la racine vers /home
        return $this->redirectToRoute('app_home');
    }
    #[Route('/home', name: 'app_home')]

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
