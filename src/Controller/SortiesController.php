<?php

namespace App\Controller;

use App\Entity\Sorties;
use App\Form\SortiesType;
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

    public function __construct(private Security $security)
    {
    }

    #[Route('/', name: '_index', methods: ['GET'])]
    public function index(SortiesRepository $sortiesRepository): Response
    {
        return $this->render('sorties/index.html.twig', [
            'sorties' => $sortiesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: '_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sortie = new Sorties();
        $form = $this->createForm(SortiesType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->security->getUser();

            $sortie->setNoParticipant($user);

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
        return $this->render('sorties/show.html.twig', [
            'sortie' => $sortie,
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
}
