<?php

namespace App\Controller;

use App\Entity\Inscriptions;
use App\Entity\Participant;
use App\Entity\Sorties;
use App\Form\SortiesType;
use App\Repository\EtatsRepository;
use App\Repository\InscriptionsRepository;
use App\Repository\SortiesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sorties', name: 'app_sorties')]
class SortiesController extends AbstractController
{
    public function __construct(
        private readonly Security $security,
        private readonly SortiesRepository $sortiesRepository,
        private readonly EtatsRepository $etatsRepository,
    ) {}

    #[Route('/', name: '_index', methods: ['GET'])]
    public function index(SortiesRepository $sortiesRepository): Response
    {
        $sorties = $sortiesRepository->findAll();
        $cptBySortie = [];

        foreach ($sorties as $sortie) {
            $inscriptions = $sortie->getInscriptions();
            $cptBySortie[$sortie->getId()] = count($inscriptions);
        }

        return $this->render('sorties/index.html.twig', [
            'sorties' => $sorties,
            'cpt' => $cptBySortie,
        ]);
    }

    #[Route('/new', name: '_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SortiesRepository $sortiesRepository): Response
    {
        $sortie = new Sorties();
        $form = $this->createForm(SortiesType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->security->getUser();

            $sortie->setNoParticipant($user);
            $duree = $sortiesRepository->calculateDuration($sortie->getDateDebut(), $sortie->getDateClotureInscription());
            $sortie->setDuree($duree);

            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->redirectToRoute('app_sorties_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sorties/new.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: '_show', methods: ['GET'])]
    public function show(Sorties $sortie, InscriptionsRepository $inscriptionsRepository, EtatsRepository $etatsRepository): Response
    {
        $user = $this->getUser(); // Utilisateur connecté
        $inscriptions = $sortie->getInscriptions(); // Toutes les inscriptions de la sortie
        $cpt = $inscriptions->count(); // Compteur d'inscriptions
        $etat = $etatsRepository->updateEtats($sortie);

        // Vérifie si l'utilisateur est connecté et s'il est inscrit à la sortie
        $isInscrit = false;
        if ($user instanceof Participant) {
            $inscriptionExistante = $inscriptionsRepository->findOneBy([
                'noParticipant' => $user,
                'noSortie' => $sortie,
            ]);

            $isInscrit = ($inscriptionExistante !== null);
        }

        return $this->render('sorties/show.html.twig', [
            'sortie' => $sortie,
            'inscriptions' => $inscriptions,
            'cpt' => $cpt,
            'isInscrit' => $isInscrit, // Passe la variable à la vue
            'etat' => $etat,
        ]);
    }

    #[Route('/{id}/edit', name: '_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sorties $sortie, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SortiesType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_sorties_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sorties/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: '_delete', methods: ['POST'])]
    public function delete(Request $request, Sorties $sortie, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sortie->getId(), $request->get('_token'))) {
            $entityManager->remove($sortie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_sorties_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/inscription', name: '_inscription', methods: ['GET', 'POST'])]
    public function registration(Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        $inscription = new Inscriptions();
        $now = new \DateTime();
        $user = $this->getUser();

        if (!$user instanceof Participant) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour vous inscrire.');
        }

        $sortie = $this->sortiesRepository->findOneBy(['id' => $id]);

        if (!$sortie) {
            throw $this->createNotFoundException('La sortie n\'existe pas.');
        }

        $inscriptionExistante = $entityManager->getRepository(Inscriptions::class)->findOneBy([
            'noParticipant' => $user,
            'noSortie' => $sortie,
        ]);

        if ($inscriptionExistante) {
            $this->addFlash('warning', 'Vous êtes déjà inscrit à cette sortie.');
            return $this->redirectToRoute('app_sorties_show', ['id' => $id]);
        }

        $inscription->setNoParticipant($user);
        $inscription->setDateInscription($now);
        $inscription->setNoSortie($sortie);
        $entityManager->persist($inscription);
        $entityManager->flush();

        $this->addFlash('success', 'Vous êtes inscrit à la sortie.');

        return $this->redirectToRoute('app_sorties_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/desinscription', name: '_desinscription', methods: ['GET', 'POST'])]
    public function unregistration(Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        $user = $this->getUser();

        if (!$user instanceof Participant) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour vous désinscrire.');
        }

        $sortie = $this->sortiesRepository->findOneBy(['id' => $id]);

        if (!$sortie) {
            throw $this->createNotFoundException('La sortie n\'existe pas.');
        }

        $inscriptionExistante = $entityManager->getRepository(Inscriptions::class)->findOneBy([
            'noParticipant' => $user,
            'noSortie' => $sortie,
        ]);

        if (!$inscriptionExistante) {
            $this->addFlash('warning', 'Vous n\'êtes pas inscrit à cette sortie.');
            return $this->redirectToRoute('app_sorties_show', ['id' => $id]);
        }

        $entityManager->remove($inscriptionExistante);
        $entityManager->flush();

        $this->addFlash('success', 'Vous avez été désinscrit de la sortie.');

        return $this->redirectToRoute('app_sorties_index', [], Response::HTTP_SEE_OTHER);
    }
}
