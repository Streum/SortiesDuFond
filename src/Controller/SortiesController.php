<?php

namespace App\Controller;

use App\Entity\Inscriptions;
use App\Entity\Participant;
use App\Entity\Sorties;
use App\Form\SortiesType;
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

    public function __construct(private Security $security, private SortiesRepository $sortiesRepository, private InscriptionsRepository $inscriptionsRepository)
    {
    }

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
            $duree = $sortiesRepository->calculateDuration($sortie->getDateDebut(), $sortie->getDateCloture());
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
    public function show(Sorties $sortie): Response
    {
        $inscriptions = $sortie->getInscriptions();
        $cpt = $inscriptions->count();
        return $this->render('sorties/show.html.twig', [
            'sortie' => $sortie,
            'inscriptions' => $inscriptions,
            'cpt' => $cpt,
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
        if ($this->isCsrfTokenValid('delete'.$sortie->getId(), $request->getPayload()->getString('_token'))) {
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

        $sortie = $this->sortiesRepository->findOneBySomeField($id);

        if (!$sortie) {
            throw $this->createNotFoundException('La sortie n\'existe pas.');
        }

        $inscriptionExistante = $entityManager->getRepository(Inscriptions::class)->findOneBy([
            'noParticipant' => $user,
            'noSortie' => $sortie,
        ]);

        if ($inscriptionExistante) {
            $this->addFlash('warning', 'Vous êtes déjà inscrit à cette sortie.');
            return $this->redirectToRoute('app_sortie_show', ['id' => $id]);
        }

        if ($user && $sortie){
            $inscription->setNoParticipant($user);
            $inscription->setDateInscription($now);
            $inscription->setNoSortie($sortie);
            $entityManager->persist($inscription);
            $entityManager->flush();
        } else {
            throw $this->createNotFoundException('Sortie inexistante');
        }

        $this->addFlash('success', 'Vous êtes inscrit à la sortie.');

        return $this->redirectToRoute('app_sorties_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/desinscription', name: '_desinscription', methods: ['GET','POST'])]
    public function unregistration(Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        $user = $this->getUser();

        // Vérification si l'utilisateur est connecté et est un Participant
        if (!$user instanceof Participant) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour vous désinscrire.');
        }

        // Récupération de la sortie via l'id
        $sortie = $this->sortiesRepository->findOneBySomeField($id);

        // Vérification si la sortie existe
        if (!$sortie) {
            throw $this->createNotFoundException('La sortie n\'existe pas.');
        }

        // Vérification de l'existence d'une inscription pour cet utilisateur et cette sortie
        $inscriptionExistante = $entityManager->getRepository(Inscriptions::class)->findOneBy([
            'noParticipant' => $user,
            'noSortie' => $sortie,
        ]);

        if (!$inscriptionExistante) {
            $this->addFlash('warning', 'Vous n\'êtes pas inscrit à cette sortie.');
            return $this->redirectToRoute('app_sortie_show', ['id' => $id]);
        }

        // Suppression de l'inscription existante
        $entityManager->remove($inscriptionExistante);
        $entityManager->flush();

        $this->addFlash('success', 'Vous avez été désinscrit de la sortie.');

        return $this->redirectToRoute('app_sorties_index', [], Response::HTTP_SEE_OTHER);
    }

}
