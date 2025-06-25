<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\AstonishingVideo;
use App\Entity\FiercePublisher;
use App\Entity\StunningImage;
use App\Entity\User;
use App\Entity\UserInfo;
use App\Entity\WonderfullBook;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Créer des utilisateurs avec leurs UserInfo
        $users = [];
        for ($i = 0; $i < 5; $i++) {
            $userInfo = new UserInfo();
            $userInfo
                ->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setAddress($faker->streetAddress())
                ->setTel($faker->phoneNumber())
                ->setPostalCode($faker->postcode())
                ->setCountry($faker->country());

            $user = new User();
            $user
                ->setEmail($faker->email())
                ->setPassword($this->passwordHasher->hashPassword($user, 'password123'))
                ->setIsActive($faker->boolean(80))
                ->setUserInfo($userInfo);

            $users[] = $user;
            $manager->persist($userInfo);
            $manager->persist($user);
        }

        // Créer des éditeurs
        $publishers = [];
        for ($i = 0; $i < 10; $i++) {
            $publisher = new FiercePublisher();
            $publisher
                ->setName($faker->company())
                ->setAddress($faker->streetAddress())
                ->setTel($faker->phoneNumber())
                ->setEmail($faker->companyEmail())
                ->setPostalCode($faker->postcode())
                ->setCountry($faker->country());

            $publishers[] = $publisher;
            $manager->persist($publisher);
        }

        // Créer des vidéos
        for ($i = 0; $i < 20; $i++) {
            $video = new AstonishingVideo();
            $video
                ->setTitle($faker->sentence(3))
                ->setAuthorFirstName($faker->firstName())
                ->setAuthorLastName($faker->lastName())
                ->setRating($faker->numberBetween(1, 5))
                ->setIsPublic($faker->boolean(70))
                ->setPublishDate($faker->dateTimeThisDecade())
                ->setPublisher($faker->company())
                ->setFilepath('/videos/' . $faker->uuid() . '.mp4');

            // Associer 1 à 3 éditeurs aléatoires
            $randomPublishers = $faker->randomElements($publishers, $faker->numberBetween(1, 3));
            foreach ($randomPublishers as $publisher) {
                $video->addFiercePublisher($publisher);
            }

            $manager->persist($video);
        }

        // Créer des images
        for ($i = 0; $i < 20; $i++) {
            $image = new StunningImage();
            $image
                ->setTitle($faker->sentence(3))
                ->setAuthorFirstName($faker->firstName())
                ->setAuthorLastName($faker->lastName())
                ->setRating($faker->numberBetween(1, 5))
                ->setIsPublic($faker->boolean(70))
                ->setPrice($faker->randomFloat(2, 5, 100))
                ->setPublishedDate($faker->dateTimeThisDecade())
                ->setPublisher($faker->company())
                ->setFilepath('/images/' . $faker->uuid() . '.jpg');

            // Associer 1 à 3 éditeurs aléatoires
            $randomPublishers = $faker->randomElements($publishers, $faker->numberBetween(1, 3));
            foreach ($randomPublishers as $publisher) {
                $image->addFiercePublisher($publisher);
            }

            $manager->persist($image);
        }

        // Créer des livres
        for ($i = 0; $i < 20; $i++) {
            $book = new WonderfullBook();
            $book
                ->setTitle($faker->sentence(3))
                ->setAuthorFirstName($faker->firstName())
                ->setAuthorLastName($faker->lastName())
                ->setRating($faker->numberBetween(1, 5))
                ->setIsPublic($faker->boolean(70))
                ->setPrice($faker->randomFloat(2, 10, 50))
                ->setPublishedDate($faker->dateTimeThisDecade())
                ->setGenre($faker->randomElement(['Fiction', 'Non-fiction', 'Science-fiction', 'Fantasy', 'Biography']))
                ->setPublisher($faker->company())
                ->setIsbn($faker->isbn13());

            // Associer 1 à 3 éditeurs aléatoires
            $randomPublishers = $faker->randomElements($publishers, $faker->numberBetween(1, 3));
            foreach ($randomPublishers as $publisher) {
                $book->addFiercePublisher($publisher);
            }

            $manager->persist($book);
        }

        $manager->flush();
    }
}