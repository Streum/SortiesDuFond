<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantAdminEditType;
use App\Form\ParticipantEditType;
use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use App\Repository\SortiesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;


class ParticipantController extends AbstractController
{
    #[Route('/participant/myProfile', name: 'app_user', methods: ['GET', 'POST'])]
    public function infoProfile(Security $security, EntityManagerInterface $entityManager, SortiesRepository $sortiesRepository): Response
    {
        // Récupérer l'utilisateur connecté
        $participant = $security->getUser();


        // Vérifier que l'utilisateur est connecté
        if (!$participant) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }
        if ($participant && $participant instanceof Participant) {
            // Exemple : ajouter ROLE_ADMIN si la condition est remplie
            if ($participant->isAdministrateur()) {
                $participant->setRoles(['ROLE_ADMIN']);

                $entityManager->persist($participant);
                $entityManager->flush();
            }
        }
        // Passer l'utilisateur au template
        return $this->render('participant/show.html.twig', [
            'participant' => $participant,
        ]);
    }
    #[Route('/participant/edit', name: 'app_participant_edit_profil', methods: ['GET', 'POST'])]
    public function editProfil(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, Security $security): Response
    {
        // Récupérer l'utilisateur connecté
        $participant = $security->getUser();

        if ($participant->isAdministrateur()) {
            $form = $this->createForm(ParticipantEditType::class, $participant);
        } else {
            $form = $this->createForm(ParticipantType::class, $participant);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'upload de la photo
            /** @var UploadedFile $photoFile */
            $photoFile = $form->get('photo')->getData();

            if ($photoFile) {
                $newFilename = uniqid().'.'.$photoFile->guessExtension();

                try {
                    $photoFile->move(
                        $this->getParameter('photos_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer l'erreur si le fichier ne peut pas être déplacé
                }

                $participant->setPhoto($newFilename);
            }
            // Vérifiez si le mot de passe a été modifié
            if ($form->get('plainPassword')->getData()) {
                $participant->setPassword(
                    $userPasswordHasher->hashPassword(
                        $participant,
                        $form->get('plainPassword')->getData()
                    )
                );
            }

            // Enregistrez les modifications dans la base de données
            $entityManager->flush();

            // Redirection après succès
            return $this->redirectToRoute('app_user', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('participant/edit.html.twig', [
            'participant' => $participant,
            'form' => $form->createView(), // Corrigez ici pour passer le formulaire à la vue
        ]);
    }

    #[Route('/administration/participant', name: 'app_participant_index', methods: ['GET'])]
    public function index(ParticipantRepository $participantRepository): Response
    {
        return $this->render('participant/index.html.twig', [
            'participants' => $participantRepository->findAll(),
        ]);
    }



    #[Route('/administration/participant/{id}', name: 'app_participant_show', methods: ['GET'])]
    public function show(Participant $participant, SortiesRepository $sortiesRepository): Response
    {
        $sorties = $sortiesRepository->findAll();
        foreach ($sorties as $sortie) {
            if ($sortie->getNoParticipant()===$participant)
            $participant->addSortie($sortie);
        }
        return $this->render('participant/show.html.twig', [
            'participant' => $participant,
        ]);
    }
    #[Route('/administration/new', name: 'app_participant_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $participant = new Participant();
        $form = $this->createForm(ParticipantEditType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            // Gestion de l'upload de la photo
            /** @var UploadedFile $photoFile */
            $photoFile = $form->get('photo')->getData();

            if ($photoFile) {
                $newFilename = uniqid().'.'.$photoFile->guessExtension();

                try {
                    $photoFile->move(
                        $this->getParameter('photos_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer l'erreur si le fichier ne peut pas être déplacé
                }

                $participant->setPhoto($newFilename);
            }
            $participant->setPassword(
                $userPasswordHasher->hashPassword(
                    $participant,
                    $form->get('plainPassword')->getData()
                )
            );
            // Mettre à jour le champ `administrateur` en fonction du rôle
            $isAdmin = in_array('ROLE_ADMIN', $participant->getRoles(), true);
            $participant->setAdministrateur($isAdmin);
            $entityManager->persist($participant);
            $entityManager->flush();

            return $this->redirectToRoute('app_participant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('participant/new.html.twig', [
            'participant' => $participant,
            'form' => $form,
        ]);
    }
    #[Route('/administration/participant/edit/{id}', name: 'app_participant_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Participant $participant, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ParticipantAdminEditType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'upload de la photo
            /** @var UploadedFile $photoFile */
            $photoFile = $form->get('photo')->getData();

            if ($photoFile) {
                $newFilename = uniqid().'.'.$photoFile->guessExtension();

                try {
                    $photoFile->move(
                        $this->getParameter('photos_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer l'erreur si le fichier ne peut pas être déplacé
                }

                $participant->setPhoto($newFilename);
            }
            // Mettre à jour le champ `administrateur` en fonction du rôle
            $isAdmin = in_array('ROLE_ADMIN', $participant->getRoles(), true);
            $participant->setAdministrateur($isAdmin);
            $entityManager->flush();

            return $this->redirectToRoute('app_participant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('participant/edit.html.twig', [
            'participant' => $participant,
            'form' => $form,
        ]);
    }



    #[Route('/administration/participant/delete/{id}', name: 'app_participant_delete', methods: ['POST'])]
    public function delete(Request $request, Participant $participant, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$participant->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($participant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_participant_index', [], Response::HTTP_SEE_OTHER);
    } #[Route('/administration/participant/actif/{id}', name: 'app_participant_actif', methods: ['POST', 'GET'])]
    public function setActif(Request $request, Participant $participant, EntityManagerInterface $entityManager): Response
    {
            $participant->setActif(!$participant->isActif());
            $entityManager->flush();
        return $this->redirectToRoute('app_participant_index', [], Response::HTTP_SEE_OTHER);
    }
}
