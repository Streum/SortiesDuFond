<?php

namespace App\Controller;

use App\Entity\GroupePrive;
use App\Entity\InscriptionGroupePrive;
use App\Form\GroupePriveType;
use App\Repository\GroupePriveRepository;
use App\Repository\InscriptionGroupePriveRepository;
use App\Repository\InscriptionsRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/groupe_prive')]
class GroupePriveController extends AbstractController
{
    #[Route('/', name: 'app_groupe_prive_index', methods: ['GET'])]
    public function index(Security $security, GroupePriveRepository $groupePriveRepository, InscriptionGroupePriveRepository $inscriptionGroupePriveRepository): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $security->getUser();

        // Récupérer les groupes appartenant à l'utilisateur connecté
        $ownedGroupePrives = $groupePriveRepository->findBy(['owner' => $user]);

        // Récupérer les groupes où l'utilisateur est inscrit
        $inscriptions = $inscriptionGroupePriveRepository->findBy(['noParticipant' => $user]);

        // Extraire les groupes des inscriptions
        $subscribedGroupePrives = [];
        foreach ($inscriptions as $inscription) {
            $subscribedGroupePrives[] = $inscription->getNoGroupe();
        }

        // Fusionner les deux listes de groupes et enlever les doublons si nécessaire
        $allGroupePrives = array_merge($ownedGroupePrives, $subscribedGroupePrives);
        $allGroupePrives = array_unique($allGroupePrives, SORT_REGULAR);

        return $this->render('groupe_prive/index.html.twig', [
            'groupe_prives' => $allGroupePrives,
        ]);
    }


    #[Route('/new', name: 'app_groupe_prive_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $groupePrive = new GroupePrive();
        $groupePrive->setOwner($this->getUser());
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

    #[Route('/{id}', name: 'app_groupe_prive_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(GroupePrive $groupePrive, ParticipantRepository $participantRepository,
                         InscriptionGroupePriveRepository $inscriptionGroupePriveRepository): Response
    {
        // Vérifier que l'utilisateur connecté est le propriétaire du groupe
        if ($groupePrive->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Access Denied');
        }
        // Récupérer tous les participants
        $participants = $participantRepository->findAll();

        // Créer un tableau de choix pour le formulaire
        $lesInscrits = [];
        foreach ($participants as $participant) {
            // Vérifier si le participant est déjà inscrit dans ce groupe privé
            $isAlreadyInscribed = $inscriptionGroupePriveRepository->findOneBy([
                'noParticipant' => $participant->getId(),
                'noGroupe' => $groupePrive->getId()
            ]);

            // Si le participant est inscrit, on l'ajoute
            if ($isAlreadyInscribed) {
                $lesInscrits[$participant->getPseudo()] = $participant;
            }
        }
        return $this->render('groupe_prive/show.html.twig', [
            'groupe_prive' => $groupePrive,
            'lesInscrits' => $lesInscrits,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_groupe_prive_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(
        Request $request,
        GroupePrive $groupePrive,
        EntityManagerInterface $entityManager,
        ParticipantRepository $participantRepository,
        InscriptionGroupePriveRepository $inscriptionGroupePriveRepository,
    ): Response
    {
        // Vérifier que l'utilisateur connecté est le propriétaire du groupe
        if ($groupePrive->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Access Denied');
        }

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

    #[Route('/{id}', name: 'app_groupe_prive_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, GroupePrive $groupePrive, EntityManagerInterface $entityManager, InscriptionGroupePriveRepository $inscriptionGroupePriveRepository): Response
    {
        // Vérifier que l'utilisateur connecté est bien le propriétaire du groupe
        if ($groupePrive->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Accès refusé');
        }

        // Valider le token CSRF
        if ($this->isCsrfTokenValid('delete'.$groupePrive->getId(), $request->request->get('_token'))) {

            // Récupérer les inscriptions liées à ce groupe privé
            $lesInscriptions = $inscriptionGroupePriveRepository->findBy(['groupePrive' => $groupePrive]);

            // Supprimer toutes les inscriptions liées au groupe, si nécessaire
            foreach ($lesInscriptions as $inscription) {
                $entityManager->remove($inscription);
            }

            // Supprimer le groupe lui-même
            $entityManager->remove($groupePrive);
            $entityManager->flush();
        }

        // Rediriger vers la liste des groupes privés
        return $this->redirectToRoute('app_groupe_prive_index', [], Response::HTTP_SEE_OTHER);
    }



    #[Route('/addParticipants/{id}', name: 'app_groupe_prive_addParticipants', requirements: ['id' => '\d+'])]
    public function addParticipants(
        Request $request,
        GroupePrive $groupePrive,
        EntityManagerInterface $entityManager,
        ParticipantRepository $participantRepository,
        FormFactoryInterface $formFactory,
        InscriptionGroupePriveRepository $inscriptionGroupePriveRepository
    ): Response
    {
        // Vérifier que l'utilisateur connecté est le propriétaire du groupe
        if ($groupePrive->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Access Denied');
        }
        // Récupérer tous les participants
        $participants = $participantRepository->findAll();

        // Créer un tableau de choix pour le formulaire
        $choices = [];
        foreach ($participants as $participant) {
            // Vérifier si le participant est déjà inscrit dans ce groupe privé
            $isAlreadyInscribed = $inscriptionGroupePriveRepository->findOneBy([
                'noParticipant' => $participant->getId(),
                'noGroupe' => $groupePrive->getId()
            ]);

            // Si le participant n'est pas encore inscrit, on l'ajoute aux choix
            if (!$isAlreadyInscribed) {
                $choices[$participant->getPseudo()] = $participant->getId(); // Utiliser l'ID comme valeur
            }
        }

        // Créer le formulaire
        $form = $formFactory->createBuilder(FormType::class)
            ->add('participants', ChoiceType::class, [
                'choices' => $choices,
                'expanded' => true,  // Utilisation des cases à cocher
                'multiple' => true,  // Permettre la sélection multiple
                'label' => 'Choisir les participants',
                'choice_attr' => function($choice, $key, $value) {
                    return ['class' => 'custom-checkbox'];
                },
            ])
            ->add('submit', SubmitType::class, ['label' => 'Ajouter participants'])
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
            return $this->redirectToRoute('app_groupe_prive_show', [
                'id' => $groupePrive->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        // Rendre le formulaire dans le template
        return $this->render('groupe_prive/ajout_participant.html.twig', [
            'groupe_prive' => $groupePrive,
            'form' => $form->createView(),
        ]);
    }
    #[Route('/deleteParticipants/{id}', name: 'app_groupe_prive_deleteParticipants', requirements: ['id' => '\d+'])]
    public function deleteParticipants(
        Request $request,
        GroupePrive $groupePrive,
        EntityManagerInterface $entityManager,
        ParticipantRepository $participantRepository,
        FormFactoryInterface $formFactory,
        InscriptionGroupePriveRepository $inscriptionGroupePriveRepository
    ): Response
    {
        // Vérifier que l'utilisateur connecté est le propriétaire du groupe
        if ($groupePrive->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Accès refusé');
        }
        // Récupérer tous les participants
        $participants = $participantRepository->findAll();

        // Créer un tableau de choix pour le formulaire
        $choices = [];
        foreach ($participants as $participant) {
            // Vérifier si le participant est déjà inscrit dans ce groupe privé
            $isAlreadyInscribed = $inscriptionGroupePriveRepository->findOneBy([
                'noParticipant' => $participant->getId(),
                'noGroupe' => $groupePrive->getId()
            ]);

            // Si le participant n'est pas encore inscrit, on l'ajoute aux choix
            if ($isAlreadyInscribed) {
                $choices[$participant->getPseudo()] = $participant->getId(); // Utiliser l'ID comme valeur
            }
        }

        // Créer le formulaire
        $form = $formFactory->createBuilder(FormType::class)
            ->add('participants', ChoiceType::class, [
                'choices' => $choices,
                'expanded' => true,  // Utilisation des cases à cocher
                'multiple' => true,  // Permettre la sélection multiple
                'label' => 'Select Participants',
            ])
            ->add('submit', SubmitType::class, ['label' => 'Supprimer les participants'])
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
                    // Trouver l'inscription existante pour ce participant et ce groupe privé
                    $inscription = $inscriptionGroupePriveRepository->findOneBy([
                        'noGroupe' => $groupePrive,
                        'noParticipant' => $participant
                    ]);

                    // Si une inscription existe, la supprimer
                    if ($inscription) {
                        $entityManager->remove($inscription);
                    }
                }
            }

            // Sauvegarder les nouvelles inscriptions en base de données
            $entityManager->flush();

            // Rediriger après l'inscription
            return $this->redirectToRoute('app_groupe_prive_show', [
                'id' => $groupePrive->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        // Rendre le formulaire dans le template
        return $this->render('groupe_prive/delete_participant.html.twig', [
            'groupe_prive' => $groupePrive,
            'form' => $form->createView(),
        ]);
    }

}
