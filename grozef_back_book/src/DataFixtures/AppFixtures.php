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

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Create publishers
        $publishers = [];
        for ($i = 0; $i < 10; $i++) {
            $publisher = new FiercePublisher();
            $publisher->setName($faker->company);
            $publisher->setAddress($faker->address);
            $publisher->setTel($faker->phoneNumber);
            $publisher->setMail($faker->email);
            $publisher->setPostalCode($faker->postcode);
            $publisher->setCountry($faker->country);
            $publishers[] = $publisher;
            $manager->persist($publisher);
        }

        // Create users
        $users = [];
        for ($i = 0; $i < 5; $i++) {
            $userInfo = new UserInfo();
            $userInfo->setFirstName($faker->firstName);
            $userInfo->setLastName($faker->lastName);
            $userInfo->setAddress($faker->address);
            $userInfo->setTel($faker->phoneNumber);
            $userInfo->setPostalCode($faker->postcode);
            $userInfo->setCountry($faker->country);

            $user = new User();
            $user->setMail($faker->email);
            $user->setPassword($faker->password(8, 255)); // Unhashed password
            $user->setIsActive($faker->boolean(80));
            $user->setUserInfo($userInfo);

            $users[] = $user;
            $manager->persist($userInfo);
            $manager->persist($user);
        }

        // Create videos
        for ($i = 0; $i < 20; $i++) {
            $video = new AstonishingVideo();
            $video->setTitle($faker->sentence(2));
            $video->setAuthorFirstName($faker->firstName);
            $video->setAuthorLastName($faker->lastName);
            $video->setRating($faker->numberBetween(0, 5));
            $video->setIsPublic($faker->boolean(70));
            $video->setPublishDate($faker->dateTimeThisDecade);
            $video->setPublisher($faker->company);
            $video->setFilepath(sprintf('/videos/%s.mp4', $faker->slug));

            // Associate 1 or 2 random publishers
            $randomPublishers = $faker->randomElements($publishers, $faker->numberBetween(1, 2));
            foreach ($randomPublishers as $publisher) {
                $video->addFiercePublisher($publisher);
            }

            $manager->persist($video);
        }

        // Create images
        for ($i = 0; $i < 20; $i++) {
            $image = new StunningImage();
            $image->setTitle($faker->sentence(2));
            $image->setAuthorFirstName($faker->firstName());
            $image->setAuthorLastName($faker->lastName());
            $image->setRating($faker->numberBetween(0, 5));
            $image->setIsPublic($faker->boolean(70));
            $image->setPrice($faker->randomFloat(2, 5, 100));
            $image->setPublishedDate($faker->dateTimeThisDecade());
            $image->setPublisher($faker->company());
            $image->setFilepath(sprintf('/images/%s.jpg', $faker->slug()));

            // Associate 1 or 2 random publishers
            $randomPublishers = $faker->randomElements($publishers, $faker->numberBetween(1, 2));
            foreach ($randomPublishers as $publisher) {
                $image->addFiercePublisher($publisher);
            }

            $manager->persist($image);
        }

        // Create books
        for ($i = 0; $i < 20; $i++) {
            $book = new WonderfullBook();
            $book->setTitle($faker->sentence(2));
            $book->setAuthorFirstName($faker->firstName);
            $book->setAuthorLastName($faker->lastName);
            $book->setRating($faker->numberBetween(0, 5));
            $book->setIsPublic($faker->boolean(70));
            $book->setPrice($faker->randomFloat(2, 5, 50));
            $book->setPublishedDate($faker->dateTimeThisDecade);
            $book->setGenre($faker->word);
            $book->setPublisher($faker->company);
            $book->setIsbn($faker->isbn13);

            // Associate 1 or 2 random publishers
            $randomPublishers = $faker->randomElements($publishers, $faker->numberBetween(1, 2));
            foreach ($randomPublishers as $publisher) {
                $book->addFiercePublisher($publisher);
            }

            $manager->persist($book);
        }

        // Save all entities
        $manager->flush();
    }
}
