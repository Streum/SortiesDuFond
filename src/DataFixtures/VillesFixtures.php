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
INSERT INTO villes (id, nom_ville, code_postal, url_photos) VALUES
(1, 'Rennes', 35000, 'https://www.jds.fr/medias/image/opera-de-rennes-reservation-spectacle-visite-154660-920-0-F.webp'),
(2, 'Nantes', 44000, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSJN-Sf4luiV48th6QVS3VOYb6GBpRkEcgmwA&s'),
(3, 'Niort', 79000, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ2fz-KcFuVOA2E-RaTs4dCS99ysLZJS7AHsw&s'),
(4, 'Quimper', 29000, 'https://www.trecobois.fr/wp-content/uploads/2021/07/quimper.jpg');

INSERT INTO etats (id, libelle) VALUES (1, 'Créée'), (2, 'Ouverte'), (3, 'Clôturée'), (4, 'Activité en cours'), (5, 'Passée'), (6, 'Annulée'), (7, 'Archivée');

INSERT INTO `sites` (`id`, `nom_site`) VALUES (NULL, 'Rennes'), (NULL, 'Nantes'), (NULL, 'Niort'), (NULL, 'Quimper');

INSERT INTO lieux (id, no_ville_id, nom_lieu, rue, latitude, longitude) VALUES
(1, 1, 'Parc du Thabor', 'Rue des Olivettes', '48.111', '-1.678'),
(2, 1, 'Musée des Beaux-Arts', '20 Quai Émile Zola', '48.113', '-1.674'),
(3, 2, 'Château des Ducs de Bretagne', '4 Place Marc Elder', '47.215', '-1.552'),
(4, 2, 'Jardin des Plantes', 'Rue Stanislas Baudry', '47.219', '-1.543'),
(5, 3, 'Donjon de Niort', 'Place du Donjon', '46.325', '-0.461'),
(6, 3, 'Musée Bernard d’Agesci', '26 Avenue de Limoges', '46.324', '-0.459'),
(7, 4, 'Cathédrale Saint-Corentin', '1 Rue du Roi Gradlon', '47.996', '-4.096'),
(8, 4, 'Musée des Beaux-Arts de Quimper', '40 Place Saint-Corentin', '47.995', '-4.095'),
(9, 1, 'Université de Rennes 1', '2 Rue du Thabor', '48.115', '-1.670'),
(10, 2, 'Les Machines de l’île', 'Boulevard Léon Bureau', '47.206', '-1.558'),
(11, 2, 'Île de Versailles', 'Quai de Versailles', '47.223', '-1.549'),
(12, 3, 'Église Saint-André', 'Rue Saint-André', '46.324', '-0.457'),
(13, 3, 'Niort Plage', 'Chemin du Plafond', '46.329', '-0.470'),
(14, 4, 'Le Corniguel', 'Chemin du Corniguel', '48.003', '-4.116'),
(15, 4, 'Théâtre de Cornouaille', '1 Esplanade François Mitterrand', '47.995', '-4.095');

INSERT INTO participants (id, site_id, email, pseudo, nom, prenom, telephone, administrateur, actif, roles, password, photo) VALUES
(2, 1, 'goku@anime.jp', 'DarkGoku45', 'Son', 'Goku', '+81-123-456-789', 0, 1, '[]', 'password123', '/images/goku.jpg'),
(3, 1, 'vegeta@anime.jp', 'VegetaKing99', 'Vegeta', 'Vegeta', '+81-987-654-321', 0, 1, '[]', 'password123', '/images/vegeta.jpg'),
(4, 1, 'luffysama@anime.jp', 'StrawHatLuffy', 'Monkey', 'Luffy', '+81-234-567-890', 0, 1, '[]', 'password123', '/images/luffy.jpg'),
(5, 1, 'naruto@anime.jp', 'NarutoNinja77', 'Naruto', 'Uzumaki', '+81-345-678-901', 0, 1, '[]', 'password123', '/images/naruto.jpg'),
(6, 1, 'sasuke@anime.jp', 'DarkSasuke78', 'Sasuke', 'Uchiha', '+81-456-789-012', 0, 1, '[]', 'password123', '/images/sasuke.jpg'),
(7, 1, 'sakura@anime.jp', 'CherryBlossomSakura', 'Sakura', 'Haruno', '+81-567-890-123', 0, 1, '[]', 'password123', '/images/sakura.jpg'),
(8, 1, 'zoro@anime.jp', 'ZoroSamurai88', 'Roronoa', 'Zoro', '+81-678-901-234', 0, 1, '[]', 'password123', '/images/zoro.jpg'),
(9, 1, 'luffy@anime.jp', 'RubberLuffy65', 'Monkey', 'Luffy', '+81-789-012-345', 0, 1, '[]', 'password123', '/images/luffy2.jpg'),
(10, 1, 'kakashi@anime.jp', 'KakashiSensei01', 'Kakashi', 'Hatake', '+81-890-123-456', 0, 1, '[]', 'password123', '/images/kakashi.jpg'),
(11, 1, 'shikamaru@anime.jp', 'ShikamaruNara', 'Shikamaru', 'Nara', '+81-901-234-567', 0, 1, '[]', 'password123', '/images/shikamaru.jpg'),
(12, 1, 'hinata@anime.jp', 'HinataHyuga44', 'Hinata', 'Hyuga', '+81-012-345-678', 0, 1, '[]', 'password123', '/images/hinata.jpg'),
(13, 1, 'itachi@anime.jp', 'ItachiRogue66', 'Itachi', 'Uchiha', '+81-123-456-789', 0, 1, '[]', 'password123', '/images/itachi.jpg'),
(14, 1, 'kurenai@anime.jp', 'KurenaiSensei88', 'Kurenai', 'Yuhi', '+81-234-567-890', 0, 1, '[]', 'password123', '/images/kurenai.jpg'),
(15, 1, 'jiraiya@anime.jp', 'FrogSageJiraiya', 'Jiraiya', 'Sannin', '+81-345-678-901', 0, 1, '[]', 'password123', '/images/jiraiya.jpg'),
(16, 1, 'natsu@anime.jp', 'FireNatsu55', 'Natsu', 'Dragneel', '+81-456-789-012', 0, 1, '[]', 'password123', '/images/natsu.jpg'),
(17, 1, 'erza@anime.jp', 'ErzaKnight22', 'Erza', 'Scarlet', '+81-567-890-123', 0, 1, '[]', 'password123', '/images/erza.jpg'),
(18, 1, 'gray@anime.jp', 'IceGray77', 'Gray', 'Fullbuster', '+81-678-901-234', 0, 1, '[]', 'password123', '/images/gray.jpg'),
(19, 1, 'lucy@anime.jp', 'LucyFairy12', 'Lucy', 'Heartfilia', '+81-789-012-345', 0, 1, '[]', 'password123', '/images/lucy.jpg'),
(20, 1, 'gildarts@anime.jp', 'GildartsCrush', 'Gildarts', 'Clive', '+81-890-123-456', 0, 1, '[]', 'password123', '/images/gildarts.jpg'),
(21, 1, 'maka@anime.jp', 'MakaChop99', 'Maka', 'Albarn', '+81-901-234-567', 0, 1, '[]', 'password123', '/images/maka.jpg'),
(22, 1, 'soul@anime.jp', 'SoulReaper22', 'Soul', 'Eater', '+81-012-345-678', 0, 1, '[]', 'password123', '/images/soul.jpg'),
(23, 1, 'blackstar@anime.jp', 'BlackStarShuriken', 'BlackStar', 'Shuriken', '+81-123-456-789', 0, 1, '[]', 'password123', '/images/blackstar.jpg'),
(24, 1, 'tsubaki@anime.jp', 'TsubakiWeapon33', 'Tsubaki', 'Nakatsukasa', '+81-234-567-890', 0, 1, '[]', 'password123', '/images/tsubaki.jpg'),
(25, 1, 'death_the_kid@anime.jp', 'DeathKid77', 'Death', 'The Kid', '+81-345-678-901', 0, 1, '[]', 'password123', '/images/death_the_kid.jpg');

INSERT INTO sorties (id, no_etat_id, no_lieu_id, no_participant_id, nom, date_debut, date_cloture_inscription, date_creation_sortie, duree, nb_inscriptions_max, description_infos, url_photo, date_fin) VALUES
(1, 1, 1, 1, 'Promenade au Parc du Thabor', '2024-11-15 10:00:00', '2024-11-10 12:00:00', '2024-11-01 09:00:00', '120', '20', 'Une belle promenade matinale au Parc du Thabor.', '/images/thabor.jpg', '2024-11-15 12:00:00'),
(2, 1, 2, 1, 'Visite du Musée des Beaux-Arts', '2024-12-05 14:00:00', '2024-12-01 12:00:00', '2024-11-15 09:00:00', '120', '15', 'Découverte des œuvres classiques au Musée des Beaux-Arts.', '/images/musee.jpg', '2024-12-05 16:00:00'),
(3, 1, 3, 1, 'Exploration du Donjon de Niort', '2024-12-10 10:00:00', '2024-12-05 12:00:00', '2024-11-20 10:00:00', '120', '20', 'Montée dans l’historique Donjon de Niort.', '/images/donjon.jpg', '2024-12-10 12:00:00'),
(4, 1, 4, 1, 'Détente au Jardin des Plantes', '2024-12-15 11:00:00', '2024-12-10 12:00:00', '2024-11-25 10:00:00', '120', '25', 'Un moment de détente au cœur de la nature.', '/images/jardin.jpg', '2024-12-15 13:00:00'),
(5, 1, 5, 1, 'Découverte du Château des Ducs de Bretagne', '2024-12-20 09:00:00', '2024-12-15 12:00:00', '2024-12-01 10:00:00', '120', '30', 'Visite historique du célèbre Château des Ducs.', '/images/chateau.jpg', '2024-12-20 11:00:00'),
(6, 2, 6, 1, 'Visite du Musée Bernard d’Agesci', '2024-10-01 15:00:00', '2024-09-25 12:00:00', '2024-09-10 09:00:00', '120', '18', 'Découverte des collections du Musée Bernard d’Agesci.', '/images/bernard.jpg', '2024-10-01 17:00:00'),
(7, 2, 7, 1, 'Découverte de la Cathédrale Saint-Corentin', '2024-10-05 09:00:00', '2024-10-01 12:00:00', '2024-09-15 10:00:00', '120', '22', 'Visite guidée de la magnifique cathédrale.', '/images/cathedrale.jpg', '2024-10-05 11:00:00'),
(8, 2, 8, 1, 'Art et histoire au Musée des Beaux-Arts de Quimper', '2024-10-10 14:00:00', '2024-10-05 12:00:00', '2024-09-20 10:00:00', '120', '15', 'Une plongée dans l’art breton.', '/images/art_quimper.jpg', '2024-10-10 16:00:00'),
(9, 2, 9, 1, 'Visite de l’Université de Rennes 1', '2024-10-15 10:00:00', '2024-10-10 12:00:00', '2024-09-25 10:00:00', '120', '20', 'Visite guidée de l’Université de Rennes 1.', '/images/universite.jpg', '2024-10-15 12:00:00'),
(10, 2, 10, 1, 'Les Machines de l’île en action', '2024-10-20 10:30:00', '2024-10-15 12:00:00', '2024-10-05 10:00:00', '120', '30', 'Voir les éléphants géants et les machines fantastiques en action.', '/images/machines.jpg', '2024-10-20 12:30:00'),
(11, 3, 11, 1, 'Balade sur l’Île de Versailles', '2024-08-01 11:00:00', '2024-07-25 12:00:00', '2024-07-01 09:00:00', '120', '25', 'Une balade tranquille sur l’Île de Versailles à Nantes.', '/images/versailles.jpg', '2024-08-01 13:00:00'),
(12, 3, 12, 1, 'Visite de l’Église Saint-André', '2024-08-10 10:00:00', '2024-08-05 12:00:00', '2024-07-20 10:00:00', '120', '20', 'Visite de l’Église Saint-André à Niort.', '/images/eglise.jpg', '2024-08-10 12:00:00'),
(13, 3, 13, 1, 'Découverte de Niort Plage', '2024-08-15 10:00:00', '2024-08-10 12:00:00', '2024-07-25 10:00:00', '120', '20', 'Découverte de Niort Plage.', '/images/plage.jpg', '2024-08-15 12:00:00'),
(14, 3, 14, 1, 'Visite du Corniguel', '2024-08-20 10:00:00', '2024-08-15 12:00:00', '2024-07-30 10:00:00', '120', '25', 'Visite du Corniguel à Quimper.', '/images/corniguel.jpg', '2024-08-20 12:00:00'),
(15, 4, 1, 1, 'Festival d’Été au Parc du Thabor', '2024-09-01 09:00:00', '2024-08-25 12:00:00', '2024-08-01 09:00:00', '720', '50', 'Festival d’Été avec plusieurs concerts et animations.', '/images/festival_thabor.jpg', '2024-09-05 20:00:00'),
(16, 4, 2, 1, 'Exposition d’art moderne au Musée des Beaux-Arts', '2024-09-01 14:00:00', '2024-08-25 12:00:00', '2024-08-01 09:00:00', '720', '30', 'Exposition d’art moderne avec des œuvres contemporaines.', '/images/expo_musee.jpg', '2024-09-05 18:00:00'),
(17, 4, 4, 1, 'Concert en plein air au Jardin des Plantes', '2024-09-01 18:00:00', '2024-08-25 12:00:00', '2024-08-01 09:00:00', '720', '200', 'Concert de musique en plein air au Jardin des Plantes.', '/images/concert_jardin.jpg', '2024-09-05 22:00:00'),
(18, 4, 5, 1, 'Marché médiéval au Donjon de Niort', '2024-09-01 10:00:00', '2024-08-25 12:00:00', '2024-08-01 09:00:00', '720', '100', 'Marché médiéval avec des artisans et des animations au Donjon.', '/images/marche_donjon.jpg', '2024-09-05 18:00:00'),
(19, 5, 6, 1, 'Visite du Musée Bernard d’Agesci', '2024-08-01 15:00:00', '2024-07-25 12:00:00', '2024-07-01 09:00:00', '120', '18', 'Découverte des collections du Musée Bernard d’Agesci.', '/images/bernard.jpg', '2024-08-01 17:00:00'),
(20, 5, 7, 1, 'Découverte de la Cathédrale Saint-Corentin', '2024-08-05 09:00:00', '2024-07-30 12:00:00', '2024-07-01 10:00:00', '120', '22', 'Visite guidée de la magnifique cathédrale.', '/images/cathedrale.jpg', '2024-08-05 11:00:00'),
(21, 7, 8, 1, 'Exposition d’art au Musée des Beaux-Arts de Quimper', '2024-07-01 14:00:00', '2024-06-25 12:00:00', '2024-06-01 09:00:00', '120', '20', 'Exposition d’art au Musée des Beaux-Arts de Quimper.', '/images/expo_quimper.jpg', '2024-07-01 16:00:00'),
(22, 7, 10, 1, 'Visite des Machines de l’île', '2024-07-15 10:30:00', '2024-07-10 12:00:00', '2024-06-15 10:00:00', '120', '30', 'Visite des Machines de l’île à Nantes.', '/images/machines.jpg', '2024-07-15 12:30:00'),
(23, 6, 12, 1, 'Sortie au bar', '2024-08-10 10:00:00', '2024-08-05 12:00:00', '2024-07-20 10:00:00', '120', '20', 'Sortie au bar pour faire du team building.', '/images/eglise_annulee.jpg', '2024-08-10 12:00:00'),
(24, 6, 13, 1, 'Plage entre amis', '2024-08-15 10:00:00', '2024-08-10 12:00:00', '2024-07-25 10:00:00', '120', '20', 'Plage entre amis, j\'espère que la météo sera bonne.', '/images/plage_annulee.jpg', '2024-08-15 12:00:00');

INSERT INTO `inscriptions` (`no_participant_id`, `no_sortie_id`, `date_inscription`) VALUES
(2, 6, '2024-09-20 10:00:00'),
(2, 8, '2024-10-01 10:00:00'),
(2, 15, '2024-09-01 09:00:00'),
(2, 18, '2024-09-01 10:00:00'),
(3, 6, '2024-09-20 11:00:00'),
(3, 8, '2024-10-01 11:00:00'),
(3, 15, '2024-09-01 09:00:00'),
(3, 18, '2024-09-01 10:00:00'),
(4, 6, '2024-09-21 10:00:00'),
(4, 9, '2024-10-05 10:00:00'),
(4, 15, '2024-09-01 09:00:00'),
(4, 18, '2024-09-01 10:00:00'),
(5, 6, '2024-09-21 11:00:00'),
(5, 9, '2024-10-05 11:00:00'),
(5, 15, '2024-09-01 09:00:00'),
(5, 18, '2024-09-01 10:00:00'),
(6, 6, '2024-09-22 09:00:00'),
(6, 9, '2024-10-06 10:00:00'),
(6, 15, '2024-09-01 09:00:00'),
(6, 18, '2024-09-01 10:00:00'),
(7, 6, '2024-09-22 10:00:00'),
(7, 9, '2024-10-06 11:00:00'),
(7, 15, '2024-09-01 09:00:00'),
(7, 18, '2024-09-01 10:00:00'),
(8, 6, '2024-09-22 11:00:00'),
(8, 9, '2024-10-07 09:00:00'),
(8, 15, '2024-09-01 09:00:00'),
(8, 18, '2024-09-01 10:00:00'),
(9, 7, '2024-09-25 10:00:00'),
(9, 9, '2024-10-07 10:00:00'),
(9, 15, '2024-09-01 09:00:00'),
(9, 18, '2024-09-01 10:00:00'),
(10, 7, '2024-09-25 11:00:00'),
(10, 9, '2024-10-07 11:00:00'),
(10, 15, '2024-09-01 09:00:00'),
(10, 18, '2024-09-01 10:00:00'),
(11, 7, '2024-09-26 10:00:00'),
(11, 9, '2024-10-08 09:00:00'),
(11, 15, '2024-09-01 09:00:00'),
(11, 18, '2024-09-01 10:00:00'),
(12, 7, '2024-09-26 11:00:00'),
(12, 9, '2024-10-08 10:00:00'),
(12, 15, '2024-09-01 09:00:00'),
(12, 18, '2024-09-01 10:00:00'),
(13, 7, '2024-09-27 09:00:00'),
(13, 9, '2024-10-08 11:00:00'),
(13, 16, '2024-09-01 14:00:00'),
(14, 7, '2024-09-27 10:00:00'),
(14, 9, '2024-10-09 09:00:00'),
(14, 16, '2024-09-01 14:00:00'),
(15, 7, '2024-09-27 11:00:00'),
(15, 9, '2024-10-09 10:00:00'),
(15, 16, '2024-09-01 14:00:00'),
(16, 7, '2024-09-28 09:00:00'),
(16, 10, '2024-10-10 10:00:00'),
(16, 16, '2024-09-01 14:00:00'),
(17, 7, '2024-09-28 10:00:00'),
(17, 10, '2024-10-10 11:00:00'),
(17, 16, '2024-09-01 14:00:00'),
(18, 7, '2024-09-28 11:00:00'),
(18, 10, '2024-10-11 10:00:00'),
(18, 16, '2024-09-01 14:00:00'),
(19, 7, '2024-09-29 09:00:00'),
(19, 10, '2024-10-11 11:00:00'),
(19, 17, '2024-09-01 18:00:00'),
(20, 7, '2024-09-29 10:00:00'),
(20, 10, '2024-10-12 09:00:00'),
(20, 17, '2024-09-01 18:00:00'),
(21, 7, '2024-09-29 11:00:00'),
(21, 10, '2024-10-12 10:00:00'),
(21, 17, '2024-09-01 18:00:00'),
(22, 7, '2024-09-30 09:00:00'),
(22, 10, '2024-10-12 11:00:00'),
(22, 17, '2024-09-01 18:00:00'),
(23, 7, '2024-09-30 10:00:00'),
(23, 10, '2024-10-12 12:00:00'),
(23, 17, '2024-09-01 18:00:00'),
(24, 8, '2024-09-30 10:00:00'),
(24, 17, '2024-09-01 18:00:00'),
(25, 8, '2024-09-30 11:00:00'),
(25, 17, '2024-09-01 18:00:00');

 */
}
