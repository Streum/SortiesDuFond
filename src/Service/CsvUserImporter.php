<?php
namespace App\Service;

use App\Entity\Participant;
use App\Entity\Sites;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CsvUserImporter
{
    private $entityManager;
    private $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    public function importUsersFromCsv(string $csvFilePath)
    {
        if (($handle = fopen($csvFilePath, 'r')) !== false) {
            // Lire l'en-tête du CSV
            $header = fgetcsv($handle, 1000, ';'); // Modifier le délimiteur si nécessaire

            if ($header === false || count($header) < 8) {
                throw new \Exception('Invalid CSV file format.');
            }

            while (($data = fgetcsv($handle, 1000, ';')) !== false) {
                // Vérifiez que chaque ligne a le nombre de colonnes attendu
                if (count($data) < 8) {
                    // Optionnel : Logger ou gérer les lignes incorrectes
                    // Affichez les données incorrectes pour déboguer
                    continue; // Passer à la ligne suivante
                }


                try {
                    $participant = new Participant();
                    $participant->setEmail($data[0]);
                    $participant->setPseudo($data[1]);
                    $participant->setNom($data[2]);
                    $participant->setPrenom($data[3]);
                    $participant->setTelephone($data[4]);

                    $hashedPassword = $this->passwordHasher->hashPassword($participant, $data[5]);
                    $participant->setPassword($hashedPassword);

                    // Récupérer le site correspondant au nom fourni
                    $site = $this->entityManager->getRepository(Sites::class)->findOneBy(['nomSite' => $data[6]]);

                    if ($site) {
                        $participant->setSite($site);
                    }


                    // Récupérer les rôles depuis le CSV et les convertir en tableau
                    $roles = explode(',', $data[7]);

                    $participant->setRoles(array_map('trim', $roles));

                    // Mettre à jour le champ `administrateur` en fonction du rôle
                    $isAdmin = in_array('ROLE_ADMIN', $participant->getRoles(), true);
                    $participant->setAdministrateur($isAdmin);


                    // Enregistrez l'utilisateur dans la base de données
                    $this->entityManager->persist($participant);


                }  catch (\Exception $e) {
                    throw new \Exception('Error' . $e->getMessage());
                }
            }

            fclose($handle);

            // Effectuez une seule fois le flush pour optimiser les performances
            $this->entityManager->flush();

        }
    }
}

