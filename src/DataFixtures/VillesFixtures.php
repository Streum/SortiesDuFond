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
}
