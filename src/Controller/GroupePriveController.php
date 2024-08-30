<?php

namespace App\Controller;

use App\Entity\GroupePrive;
use App\Entity\InscriptionGroupePrive;
use App\Form\GroupePriveType;
use App\Repository\GroupePriveRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/groupe/prive')]
class GroupePriveController extends AbstractController
{
    #[Route('/', name: 'app_groupe_prive_index', methods: ['GET'])]
    public function index(GroupePriveRepository $groupePriveRepository): Response
    {
        return $this->render('groupe_prive/index.html.twig', [
            'groupe_prives' => $groupePriveRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_groupe_prive_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $groupePrive = new GroupePrive();
        $form = $this->createForm(GroupePriveType::class, $groupePrive);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($groupePrive);
            $entityManager->flush();

            return $this->redirectToRoute('app_groupe_prive_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('groupe_prive/new.html.twig', [
            'groupe_prive' => $groupePrive,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_groupe_prive_show', methods: ['GET'])]
    public function show(GroupePrive $groupePrive): Response
    {
        return $this->render('groupe_prive/show.html.twig', [
            'groupe_prive' => $groupePrive,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_groupe_prive_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, GroupePrive $groupePrive, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GroupePriveType::class, $groupePrive);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_groupe_prive_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('groupe_prive/edit.html.twig', [
            'groupe_prive' => $groupePrive,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_groupe_prive_delete', methods: ['POST'])]
    public function delete(Request $request, GroupePrive $groupePrive, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$groupePrive->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($groupePrive);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_groupe_prive_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/addParticipants/{id}', name: 'app_groupe_prive_addParticipants')]
    public function addParticipants(Request $request, GroupePrive $groupePrive, EntityManagerInterface $entityManager, ParticipantRepository $participantRepository, FormFactoryInterface $formFactory): Response
    {
        // Récupérer tous les participants
        $participants = $participantRepository->findAll();

        // Créer un tableau de choix pour le formulaire
        $choices = [];
        foreach ($participants as $participant) {
            $choices[$participant->getPseudo()] = $participant->getId(); // Utiliser l'ID comme valeur
        }

        // Créer le formulaire
        $form = $formFactory->createBuilder(FormType::class)
            ->add('participants', ChoiceType::class, [
                'choices' => $choices,
                'expanded' => true,  // Utilisation des cases à cocher
                'multiple' => true,  // Permettre la sélection multiple
                'label' => 'Select Participants',
            ])
            ->add('submit', SubmitType::class, ['label' => 'Add Participants'])
            ->getForm();

        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les IDs des participants sélectionnés
            $selectedParticipants = $form->get('participants')->getData();

            // Créer les inscriptions pour les participants sélectionnés
            foreach ($selectedParticipants as $participantId) {
                $participant = $participantRepository->find($participantId);
                if ($participant) {
                    $inscription = new InscriptionGroupePrive();
                    $inscription->setNoGroupe($groupePrive);
                    $inscription->setNoParticipant($participant);
                    $inscription->setDateInscription(new \DateTime());

                    $entityManager->persist($inscription);
                }
            }

            // Sauvegarder les nouvelles inscriptions en base de données
            $entityManager->flush();

            // Rediriger après l'inscription
            return $this->redirectToRoute('app_groupe_prive_index', [], Response::HTTP_SEE_OTHER);
        }

        // Rendre le formulaire dans le template
        return $this->render('groupe_prive/ajout_participant.html.twig', [
            'groupe_prive' => $groupePrive,
            'form' => $form->createView(),
        ]);
    }
}
