<?php
namespace App\DataFixtures;

use App\Entity\Participant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ParticipantFixtures extends Fixture implements FixtureGroupInterface
{
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Créer une instance de Faker
        $faker = Factory::create();

        // Générer 25 utilisateurs factices
        for ($i = 0; $i < 25; $i++) {
            $user = new Participant();
            $user->setPseudo($faker->userName);
            $user->setEmail($faker->email);
            $user->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $user,
                    $faker->password
                )
            );
            $user->setNom($faker->firstName);
            $user->setPrenom($faker->lastName);
            $user->setTelephone($faker->phoneNumber);

            // Persist l'utilisateur pour l'ajouter au lot de données à insérer
            $manager->persist($user);
        }

        // Envoyer les données à la base
        $manager->flush();
    }
    public static function getGroups(): array
    {
        return ['participant_group'];
    }

    /*
     * Commande pour lancer la création des fakers participants :
     * Symfony console doctrine:fixtures:load --group=participant_group
     */
}

