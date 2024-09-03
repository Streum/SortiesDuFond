<?php

namespace App\DataFixtures;

use App\Entity\Participant;
use App\Entity\Villes;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;


class VillesFixtures extends Fixture implements FixtureGroupInterface
{


    public function load(ObjectManager $manager): void
    {
        // Créer une instance de Faker
        $faker = Factory::create();

        // Générer 25 villes factices
        for ($i = 0; $i < 25; $i++) {
            $ville = new Villes();
            $ville->setNomVille($faker->city);
            $ville->setCodePostal($faker->postcode);


            // Persist l'utilisateur pour l'ajouter au lot de données à insérer
            $manager->persist($ville);
        }

        // Envoyer les données à la base
        $manager->flush();
    }
    public static function getGroups(): array
    {
        return ['villes_group'];
    }
    /*
    * Commande pour lancer la création des fakers villes :
    * Symfony console doctrine:fixtures:load --group=villes_group
    */

    /*
     * INSERT INTO villes (id, nom_ville, code_postal, url_photos) VALUES
(1, 'Rennes', 35000, 'https://www.jds.fr/medias/image/opera-de-rennes-reservation-spectacle-visite-154660-920-0-F.webp'),
(2, 'Nantes', 44000, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSJN-Sf4luiV48th6QVS3VOYb6GBpRkEcgmwA&s'),
(3, 'Niort', 79000, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ2fz-KcFuVOA2E-RaTs4dCS99ysLZJS7AHsw&s'),
(4, 'Quimper', 29000, 'https://www.trecobois.fr/wp-content/uploads/2021/07/quimper.jpg'),

     */

    /*
     * INSERT INTO `lieux` (`id`, `no_ville_id`, `nom_lieu`, `rue`, `latitude`, `longitude`) VALUES
(NULL, '1', 'Parc du Thabor', 'Rue des Olivettes', '48.111', '-1.678'),
(NULL, '1', 'Musée des Beaux-Arts', '20 Quai Émile Zola', '48.113', '-1.674'),
(NULL, '2', 'Château des Ducs de Bretagne', '4 Place Marc Elder', '47.215', '-1.552'),
(NULL, '2', 'Jardin des Plantes', 'Rue Stanislas Baudry', '47.219', '-1.543'),
(NULL, '3', 'Donjon de Niort', 'Place du Donjon', '46.325', '-0.461'),
(NULL, '3', 'Musée Bernard d’Agesci', '26 Avenue de Limoges', '46.324', '-0.459'),
(NULL, '4', 'Cathédrale Saint-Corentin', '1 Rue du Roi Gradlon', '47.996', '-4.096'),
(NULL, '4', 'Musée des Beaux-Arts de Quimper', '40 Place Saint-Corentin', '47.995', '-4.095'),
(NULL, '1', 'Université de Rennes 1', '2 Rue du Thabor', '48.115', '-1.670'),
(NULL, '2', 'Les Machines de l’île', 'Boulevard Léon Bureau', '47.206', '-1.558'),
(NULL, '2', 'Île de Versailles', 'Quai de Versailles', '47.223', '-1.549'),
(NULL, '3', 'Église Saint-André', 'Rue Saint-André', '46.324', '-0.457'),
(NULL, '3', 'Niort Plage', 'Chemin du Plafond', '46.329', '-0.470'),
(NULL, '4', 'Le Corniguel', 'Chemin du Corniguel', '48.003', '-4.116'),
(NULL, '4', 'Théâtre de Cornouaille', '1 Esplanade François Mitterrand', '47.995', '-4.095'),
(NULL, '1', 'Opéra de Rennes', 'Place de la Mairie', '48.110', '-1.677'),
(NULL, '1', 'Les Champs Libres', '10 Cours des Alliés', '48.106', '-1.673'),
(NULL, '2', 'Passage Pommeraye', 'Rue de la Fosse', '47.214', '-1.558'),
(NULL, '2', 'Planète Sauvage', 'La Chevalerie', '47.176', '-1.733'),
(NULL, '3', 'Aquarium de Niort', 'Avenue de la Rochelle', '46.313', '-0.461');

    ***
     */

    /*
     * INSERT INTO `sorties` (`id`, `no_etat_id`, `no_lieu_id`, `no_participant_id`, `nom`, `date_debut`, `date_cloture_inscription`, `date_creation_sortie`, `duree`, `nb_inscriptions_max`, `description_infos`, `url_photo`, `date_fin`) VALUES
(NULL, '1', '1', '1', 'Promenade au Parc du Thabor', '2023-08-01 10:00:00', '2023-07-25 12:00:00', '2023-07-01 09:00:00', '120', '20', 'Une belle promenade matinale au Parc du Thabor.', '/images/thabor.jpg', '2023-08-01 12:00:00'), -- Rennes
(NULL, '1', '2', '1', 'Visite du Musée des Beaux-Arts', '2024-08-30 14:00:00', '2024-08-28 12:00:00', '2024-08-15 09:00:00', '120', '15', 'Découverte des œuvres classiques au Musée des Beaux-Arts.', '/images/musee.jpg', '2024-08-30 16:00:00'), -- Rennes
(NULL, '1', '3', '1', 'Exploration du Château des Ducs de Bretagne', '2024-09-03 09:30:00', '2024-09-01 12:00:00', '2024-08-20 10:00:00', '120', '30', 'Visite historique du célèbre Château des Ducs.', '/images/chateau.jpg', '2024-09-03 11:30:00'), -- Nantes
(NULL, '1', '4', '1', 'Détente au Jardin des Plantes', '2024-09-04 11:00:00', '2024-09-02 12:00:00', '2024-08-21 10:00:00', '120', '25', 'Un moment de détente au cœur de la nature.', '/images/jardin.jpg', '2024-09-04 13:00:00'), -- Nantes
(NULL, '1', '5', '1', 'Tour du Donjon de Niort', '2024-09-05 10:00:00', '2024-09-03 12:00:00', '2024-08-22 10:00:00', '120', '20', 'Montée dans l’historique Donjon de Niort.', '/images/donjon.jpg', '2024-09-05 12:00:00'), -- Niort
(NULL, '1', '6', '1', 'Visite du Musée Bernard d’Agesci', '2024-09-06 15:00:00', '2024-09-04 12:00:00', '2024-08-23 10:00:00', '120', '18', 'Découverte des collections du Musée Bernard d’Agesci.', '/images/bernard.jpg', '2024-09-06 17:00:00'), -- Niort
(NULL, '1', '7', '1', 'Découverte de la Cathédrale Saint-Corentin', '2024-09-07 09:00:00', '2024-09-05 12:00:00', '2024-08-24 10:00:00', '120', '22', 'Visite guidée de la magnifique cathédrale.', '/images/cathedrale.jpg', '2024-09-07 11:00:00'), -- Quimper
(NULL, '1', '8', '1', 'Art et histoire au Musée des Beaux-Arts de Quimper', '2024-09-08 14:00:00', '2024-09-06 12:00:00', '2024-08-25 10:00:00', '120', '15', 'Une plongée dans l’art breton.', '/images/art_quimper.jpg', '2024-09-08 16:00:00'), -- Quimper
(NULL, '1', '10', '1', 'Les Machines de l’île en action', '2024-09-09 10:30:00', '2024-09-07 12:00:00', '2024-08-26 10:00:00', '120', '30', 'Voir les éléphants géants et les machines fantastiques en action.', '/images/machines.jpg', '2024-09-09 12:30:00'), -- Nantes
(NULL, '1', '11', '1', 'Balade sur l’Île de Versailles', '2024-09-10 11:00:00', '2024-09-08 12:00:00', '2024-08-27 10:00:00', '120', '25', 'Une balade tranquille sur l’Île de Versailles à Nantes.', '/images/versailles.jpg', '2024-09-10 13:00:00'); -- Nantes


     */
    /*
 *
 *
INSERT INTO `etats` (`id`, `libelle`) VALUES
(1, 'Créée'),
(2, 'Ouverte'),
(3, 'Cloturée'),
(4, 'Activité en cours'),
(5, 'passée'),
(6, 'Annulée');
 *
 *INSERT INTO `sites` (`id`, `nom_site`) VALUES (NULL, 'Rennes'), (NULL, 'Nantes'), (NULL, 'Niort'), (NULL, 'Quimper');
 */
}
