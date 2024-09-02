<?php

namespace App\Controller;

use App\Form\SearchType;
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
    public function index(Request $request, SortiesRepository $sortiesRepository, VillesRepository $villesRepository): Response
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        $sorties = [];
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $queryBuilder = $sortiesRepository->createQueryBuilder('s');

            if ($data['noLieu']) {
                $queryBuilder->join('s.noLieu', 'l')
                ->join('l.noVille', 'si')
                ->andWhere('si.id = :noLieu')
                    ->setParameter('noLieu', $data['noLieu']);
            }

            if ($data['nom']) {
                $queryBuilder->andWhere('s.nom LIKE :nom')
                    ->setParameter('nom', '%' . $data['nom'] . '%');
            }

            if ($data['dateDebut']) {
                $queryBuilder->andWhere('s.dateDebut >= :dateDebut OR s.dateFin >= :dateDebut')
                    ->setParameter('dateDebut', $data['dateDebut']);
            }

            if ($data['dateFin']) {
                $queryBuilder->andWhere('s.dateDebut <= :dateFin OR s.dateFin <= :dateFin')
                    ->setParameter('dateFin', $data['dateFin']);
            }

            if ($data['orga']) {

                $queryBuilder->join('s.noParticipant', 'p')
                    ->andWhere('p.id = :organisateur')
                    ->setParameter('organisateur', $user);
            }

            if ($data['passee']) {
                $queryBuilder->andWhere('s.dateFin < :now')
                    ->setParameter('now', new \DateTime());
            }

            if ($data['isInscrit']) {
                $queryBuilder->join('s.inscriptions', 'i')
                ->andWhere('i.noParticipant = :participant')
                    ->setParameter('participant', $user);
            }

            if ($data['isNotInscrit']) {
                $queryBuilder->leftJoin('s.inscriptions', 'i_not')
                ->andWhere('i_not.noParticipant IS NULL OR i_not.noParticipant != :participant')
                    ->setParameter('participant', $user);
            }

            $sorties = $queryBuilder->getQuery()->getResult();
        }

        return $this->render('home/index.html.twig', [
            'sorties' => $sorties,
            'villes' => $villesRepository->findAll(),
            'searchForm' => $form,
        ]);
    }#[Route('/administration', name: 'app_admin')]
    public function administration(): Response
    {
        return $this->render('participant/admin.html.twig');
    }
}
