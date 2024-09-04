<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Repository\EtatsRepository;
use App\Repository\LieuxRepository;
use App\Repository\SortiesRepository;
use App\Repository\VillesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
    public function index(Request $request, SortiesRepository $sortiesRepository, VillesRepository $villesRepository, LieuxRepository $lieuxRepository, EtatsRepository $etatsRepository): Response
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        $user = $this->getUser();

        $data = $form->isSubmitted() && $form->isValid() ? $form->getData() : [];

        $sorties = $sortiesRepository->findFilteredSorties($data, $user);

        $lieuxIds = array_map(function($sortie) {
            return $sortie->getNoLieu()->getId();
        }, $sorties);

        $lieux = $lieuxRepository->findLieuxByIds($lieuxIds);

        $lieuxById = [];
        foreach ($lieux as $lieu) {
            $lieuxById[$lieu->getId()] = $lieu;
        }

        $inscriptionsParSortie = [];
        foreach ($sorties as $sortie) {
            $inscriptionsParSortie[$sortie->getId()] = count($sortie->getInscriptions());
            //$etatsRepository->updateEtats($sortie);
        }

        return $this->render('home/index.html.twig', [
            'sorties' => $sorties,
            'lieux' => $lieuxById,
            'villes' => $villesRepository->findAll(),
            'searchForm' => $form,
            'inscriptionsParSortie' => $inscriptionsParSortie,
        ]);
    }

    #[Route('/administration', name: 'app_admin')]
    public function administration(): Response
    {
        return $this->render('participant/admin.html.twig');
    }
}
