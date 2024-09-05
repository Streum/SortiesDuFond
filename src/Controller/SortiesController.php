<?php

namespace App\Controller;

use App\Entity\Inscriptions;
use App\Entity\Lieux;
use App\Entity\Participant;
use App\Entity\Sorties;
use App\Form\AnnulationType;
use App\Form\LieuxType;
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
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/sorties', name: 'app_sorties')]
class SortiesController extends AbstractController
{
    public function __construct(
        private readonly Security          $security,
        private readonly SortiesRepository $sortiesRepository,
    )
    {
    }

    #[Route('/', name: '_index', methods: ['GET'])]
    public function index(SortiesRepository $sortiesRepository, Request $request): Response
    {
        #hello
        $page = $request->query->getInt('page', 1);
        $limit = 5; // Nombre de sorties par page

        $paginator = $sortiesRepository->findPaginatedSorties($page, $limit);
        $cptBySortie = [];

        foreach ($paginator as $sortie) {
            $inscriptions = $sortie->getInscriptions();
            $cptBySortie[$sortie->getId()] = count($inscriptions);
        }

        return $this->render('sorties/index.html.twig', [
            'cpt' => $cptBySortie,
            'sorties' => $paginator,
            'currentPage' => $page,
            'totalPages' => ceil(count($paginator) / $limit),
        ]);
    }

    #[Route('/new', name: '_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SortiesRepository $sortiesRepository): Response
    {
        $sortie = new Sorties();


        $form = $this->createForm(SortiesType::class, $sortie);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $sortie->setNoParticipant($user);

            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sorties/new.html.twig', [
            'sortie' => $sortie,
            'formS' => $form->createView()

        ]);
    }


    #[Route('/{id}', name: '_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id, InscriptionsRepository $inscriptionsRepository, EtatsRepository $etatsRepository, SortiesRepository $sortiesRepository): Response
    {

        $sortie = $sortiesRepository->findSortieWithInscriptionsAndParticipants($id);

        if (!$sortie) {
            throw $this->createNotFoundException('Sortie non trouvée');
        }

        $user = $this->getUser();
        $inscriptions = $sortie->getInscriptions();
        $cpt = $inscriptions->count();
        $etat = $etatsRepository->updateEtats($sortie);

        $isInscrit = false;
        if ($user instanceof Participant) {
            $isInscrit = $inscriptions->exists(function ($key, $inscription) use ($user) {
                return $inscription->getNoParticipant() === $user;
            });
        }

        return $this->render('sorties/show.html.twig', [
            'sortie' => $sortie,
            'inscriptions' => $inscriptions,
            'cpt' => $cpt,
            'isInscrit' => $isInscrit,
            'etat' => $etat,
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: '_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, Sorties $sortie, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if ($sortie->getNoParticipant() !== $user) {
            throw $this->createAccessDeniedException('Access Denied');
        }
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

    #[Route('/{id}', name: '_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Sorties $sortie, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if ($sortie->getNoParticipant() !== $user) {
            throw $this->createAccessDeniedException('Access Denied');
        }
        if ($this->isCsrfTokenValid('delete' . $sortie->getId(), $request->get('_token'))) {
            $entityManager->remove($sortie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_sorties_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/inscription', name: '_inscription', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
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

        if (!$sortie->peutSInscrire()) {
            $this->addFlash('error', 'Les inscriptions pour cette sortie sont fermées.');
            return $this->redirectToRoute('app_sorties_show', ['id' => $id]);
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

        return $this->redirectToRoute('app_sorties_show', ['id' => $id]);
    }

    #[Route('/{id}/desinscription', name: '_desinscription', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
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

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/publier', name: '_publication', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function publishSortie(Sorties $sortie, EtatsRepository $etatsRepository, $id): Response
    {
        $user = $this->getUser();
        if ($sortie->getNoParticipant() !== $user) {
            throw $this->createAccessDeniedException('Accès refusé');
        }

        $etat = $etatsRepository->openStatus($sortie);

        return $this->redirectToRoute('app_home', [
            'etat' => $etat,
        ], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/annuler', name: '_annuler', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function annuler(Sorties $sortie, Request $request, EntityManagerInterface $entityManager, EtatsRepository $etatsRepository): Response
    {
        $user = $this->getUser();

        // Vérifier que l'utilisateur connecté est l'organisateur de la sortie ou est administrateur
        if ($user !== $sortie->getNoParticipant() && !$user->isAdministrateur()) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à annuler cette sortie.');
            return $this->redirectToRoute('app_sorties_index');
        }


        // Vérifier que la sortie n'a pas déjà commencé
        if ($sortie->getDateDebut() <= new \DateTime()) {
            $this->addFlash('error', 'Vous ne pouvez pas annuler une sortie déjà commencée.');
            return $this->redirectToRoute('app_sorties_index');
        }

        // Créer et traiter le formulaire d'annulation
        $form = $this->createForm(AnnulationType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sortie->setMotifAnnulation($form->get('motifAnnulation')->getData());
            $sortie->setNoEtat($etatsRepository->findOneById(6));  // Assure-toi d'avoir cette méthode dans ton entité
            $entityManager->flush();

            $this->addFlash('success', 'La sortie a été annulée avec succès.');
            return $this->redirectToRoute('app_sorties_index');
        }

        return $this->render('sorties/annuler.html.twig', [
            'sortie' => $sortie,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/sorties/mes-sorties', name: '_mes_sorties', methods: ['GET'])]
    public function mySorties(): Response
    {
        $user = $this->getUser();

        if (!$user instanceof Participant) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir vos sorties.');
        }

        $sorties = $this->sortiesRepository->findSortiesByOrganizer($user);

        return $this->render('sorties/messorties.html.twig', [
            'sorties' => $sorties,
        ]);
    }

}

