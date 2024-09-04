<?php

namespace App\Controller;

use App\Entity\Lieux;
use App\Entity\Sorties;
use App\Form\LieuxType;
use App\Form\SortiesType;
use App\Repository\LieuxRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/lieux')]
class LieuxController extends AbstractController
{
    #[Route('/', name: 'app_lieux_index', methods: ['GET'])]
    public function index(LieuxRepository $lieuxRepository): Response
    {
        return $this->render('lieux/index.html.twig', [
            'lieuxes' => $lieuxRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_lieux_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $lieux = new Lieux();
        $form = $this->createForm(LieuxType::class, $lieux);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($lieux);
            $entityManager->flush();

            return $this->redirectToRoute('app_lieux_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('lieux/new.html.twig', [
            'lieux' => $lieux,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_lieux_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Lieux $lieux): Response
    {
        return $this->render('lieux/show.html.twig', [
            'lieux' => $lieux,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_lieux_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, Lieux $lieux, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LieuxType::class, $lieux);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_lieux_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('lieux/edit.html.twig', [
            'lieux' => $lieux,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_lieux_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Lieux $lieux, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$lieux->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($lieux);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_lieux_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/create_ajax', name: 'app_lieux_create_ajax')]
    public function createAjax(Request $request, EntityManagerInterface $em): Response
    {

        $lieu = new Lieux();

        $form = $this->createForm(LieuxType::class, $lieu);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $em->persist($lieu);
            $em->flush();

            $this->addFlash('success', 'Le lieu a été crée');

            return new Response(json_encode([
                'id' => $lieu->getId(),
                'name' => $lieu->getNomLieu(),
            ]), Response::HTTP_OK);
        }
        

        return $this->render('lieux/edit_ajax.html.twig', [
            'form' => $form
        ]);
    }
}
