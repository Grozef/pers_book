<?php
// declare(strict_types=1);

// namespace App\Tests\Functional;

// use App\Entity\AstonishingVideo;
// use App\Entity\FiercePublisher;
// use App\Entity\StunningImage;
// use App\Entity\User;
// use App\Entity\UserInfo;
// use App\Entity\WonderfullBook;
// use Doctrine\ORM\EntityManagerInterface;
// use Symfony\Bundle\FrameworkBundle\Console\Application;
// use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
// use Symfony\Component\Console\Input\ArrayInput;

// class EntityTest extends KernelTestCase
// {
//     private EntityManagerInterface $entityManager;

//     protected function setUp(): void
//     {
//         self::bootKernel();
//         $this->entityManager = self::getContainer()->get('doctrine.orm.entity_manager');

//         // Load fixtures using the console command
//         $application = new Application(self::$kernel);
//         $command = $application->find('doctrine:fixtures:load');
//         $input = new ArrayInput([
//             '--env' => 'test',
//             '--no-interaction' => true,
//             '--purge-with-truncate' => true,
//         ]);
//         $command->run($input, new \Symfony\Component\Console\Output\NullOutput());
//     }

//     protected function tearDown(): void
//     {
//         parent::tearDown();
//         $this->entityManager->close();
//         // $this->entityManager = null;
//     }

//     public function testPersistAndRetrieveUserWithUserInfo(): void
//     {
//         $users = $this->entityManager->getRepository(User::class)->findAll();
//         $this->assertGreaterThan(0, count($users), 'No users found in database');

//         foreach ($users as $user) {
//             $this->assertNotNull($user->getEmail(), 'User email should not be null');
//             $this->assertNotNull($user->getPassword(), 'User password should not be null');
//             $this->assertNotNull($user->getUserInfo(), 'User should have associated UserInfo');
//             $this->assertNotNull($user->getUserInfo()->getFirstName(), 'UserInfo firstName should not be null');
//             $this->assertNotNull($user->getUserInfo()->getLastName(), 'UserInfo lastName should not be null');
//         }
//     }

//     public function testPersistAndRetrieveFiercePublisher(): void
//     {
//         $publishers = $this->entityManager->getRepository(FiercePublisher::class)->findAll();
//         $this->assertGreaterThan(0, count($publishers), 'No publishers found in database');

//         foreach ($publishers as $publisher) {
//             $this->assertNotNull($publisher->getName(), 'Publisher name should not be null');
//         }
//     }

//     public function testPersistAndRetrieveAstonishingVideoWithPublishers(): void
//     {
//         $videos = $this->entityManager->getRepository(AstonishingVideo::class)->findAll();
//         $this->assertGreaterThan(0, count($videos), 'No videos found in database');

//         foreach ($videos as $video) {
//             $this->assertNotNull($video->getTitle(), 'Video title should not be null');
//             $this->assertNotNull($video->getAuthorFirstName(), 'Video authorFirstName should not be null');
//             $this->assertNotNull($video->getAuthorLastName(), 'Video authorLastName should not be null');
//             $this->assertNotNull($video->getFilepath(), 'Video filepath should not be null');
//             $this->assertGreaterThanOrEqual(0, $video->getFiercePublishers()->count(), 'Video should have associated publishers');
//         }
//     }

//     public function testPersistAndRetrieveStunningImageWithPublishers(): void
//     {
//         $images = $this->entityManager->getRepository(StunningImage::class)->findAll();
//         $this->assertGreaterThan(0, count($images), 'No images found in database');

//         foreach ($images as $image) {
//             $this->assertNotNull($image->getTitle(), 'Image title should not be null');
//             $this->assertNotNull($image->getAuthorFirstName(), 'Image authorFirstName should not be null');
//             $this->assertNotNull($image->getAuthorLastName(), 'Image authorLastName should not be null');
//             $this->assertNotNull($image->getFilepath(), 'Image filepath should not be null');
//             $this->assertGreaterThanOrEqual(0, $image->getFiercePublishers()->count(), 'Image should have associated publishers');
//         }
//     }

//     public function testPersistAndRetrieveWonderfullBookWithPublishers(): void
//     {
//         $books = $this->entityManager->getRepository(WonderfullBook::class)->findAll();
//         $this->assertGreaterThan(0, count($books), 'No books found in database');

//         foreach ($books as $book) {
//             $this->assertNotNull($book->getTitle(), 'Book title should not be null');
//             $this->assertNotNull($book->getAuthorFirstName(), 'Book authorFirstName should not be null');
//             $this->assertNotNull($book->getAuthorLastName(), 'Book authorLastName should not be null');
//             $this->assertGreaterThanOrEqual(0, $book->getFiercePublishers()->count(), 'Book should have associated publishers');
//         }
//     }

//     public function testManyToManyRelationBetweenPublisherAndVideo(): void
//     {
//         $video = new AstonishingVideo();
//         $video->setTitle('Test Video');
//         $video->setAuthorFirstName('John');
//         $video->setAuthorLastName('Doe');
//         $video->setIsPublic(true);
//         $video->setFilepath('/videos/test.mp4');

//         $publisher = new FiercePublisher();
//         $publisher->setName('Test Publisher');
//         $video->addFiercePublisher($publisher);

//         $this->entityManager->persist($publisher);
//         $this->entityManager->persist($video);
//         $this->entityManager->flush();

//         $retrievedVideo = $this->entityManager->getRepository(AstonishingVideo::class)->findOneBy(['title' => 'Test Video']);
//         $this->assertNotNull($retrievedVideo, 'Video should be persisted');
//         $this->assertCount(1, $retrievedVideo->getFiercePublishers(), 'Video should have one publisher');
//         $this->assertSame('Test Publisher', $retrievedVideo->getFiercePublishers()->first()->getName());

//         $retrievedPublisher = $this->entityManager->getRepository(FiercePublisher::class)->findOneBy(['name' => 'Test Publisher']);
//         $this->assertNotNull($retrievedPublisher, 'Publisher should be persisted');
//         $this->assertCount(1, $retrievedPublisher->getAstonishingVideos(), 'Publisher should have one video');
//     }

//     public function testOneToOneRelationBetweenUserAndUserInfo(): void
//     {
//         $userInfo = new UserInfo();
//         $userInfo->setFirstName('Alice');
//         $userInfo->setLastName('Smith');

//         $user = new User();
//         $user->setEmail('alice@example.com');
//         $user->setPassword('secret123');
//         $user->setIsActive(true);
//         $user->setUserInfo($userInfo);

//         $this->entityManager->persist($user);
//         $this->entityManager->flush();

//         $retrievedUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'alice@example.com']);
//         $this->assertNotNull($retrievedUser, 'User should be persisted');
//         $this->assertNotNull($retrievedUser->getUserInfo(), 'User should have associated UserInfo');
//         $this->assertSame('Alice', $retrievedUser->getUserInfo()->getFirstName());
//     }

//     public function testConstraintsOnRequiredFields(): void
//     {
//         $video = new AstonishingVideo();
//         $video->setIsPublic(true); // Missing required fields like title

//         try {
//             $this->entityManager->persist($video);
//             $this->entityManager->flush();
//             $this->fail('Expected an exception due to missing required fields');
//         } catch (\Exception $e) {
//             $this->assertStringContainsString('NOT NULL constraint', $e->getMessage());
//         }
//     }
// }