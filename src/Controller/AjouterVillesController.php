<?php

namespace App\Controller;

use App\Entity\Villes;
use App\Form\VillesType;
use App\Repository\VillesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AjouterVillesController extends AbstractController
{
    #[Route('/ajouter/villes', name: 'app_ajouter_villes_index', methods: ['GET'])]
    public function index(VillesRepository $villesRepository): Response
    {
        return $this->render('ajouter_villes/index.html.twig', [
            'villes' => $villesRepository->findAll(),
        ]);
    }

    #[Route('/ajouter/villes/new', name: 'app_ajouter_villes_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ville = new Villes();
        $form = $this->createForm(VillesType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ville);
            $entityManager->flush();

            return $this->redirectToRoute('app_ajouter_villes_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ajouter_villes/new.html.twig', [
            'ville' => $ville,
            'form' => $form,
        ]);
    }

    #[Route('/ajouter/villes/{id}', name: 'app_ajouter_villes_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Villes $ville): Response
    {
        return $this->render('ajouter_villes/show.html.twig', [
            'ville' => $ville,
        ]);
    }

    #[Route('/administration/ajouter/villes/{id}/edit', name: 'app_ajouter_villes_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, Villes $ville, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VillesType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_ajouter_villes_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ajouter_villes/edit.html.twig', [
            'ville' => $ville,
            'form' => $form,
        ]);
    }

    #[Route('/administration/ajouter/villes/{id}', name: 'app_ajouter_villes_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Villes $ville, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ville->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($ville);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_ajouter_villes_index', [], Response::HTTP_SEE_OTHER);
    }
}
