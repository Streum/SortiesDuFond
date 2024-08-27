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
     * INSERT INTO `villes` (`id`, `nom_ville`, `code_postal`) VALUES (NULL, 'Rennes', '35000'), (NULL, 'Nantes', '44000'), (NULL, 'Niort', '79000'), (NULL, 'Quimper', '29000');
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
     */
}
