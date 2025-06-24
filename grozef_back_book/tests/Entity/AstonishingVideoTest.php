<?php
declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\AstonishingVideo;
use App\Entity\FiercePublisher;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class AstonishingVideoTest extends TestCase
{
    private AstonishingVideo $video;

    protected function setUp(): void
    {
        $this->video = new AstonishingVideo();
    }

    public function testInitialState(): void
    {
        $this->assertNull($this->video->getId());
        $this->assertNull($this->video->getTitle());
        $this->assertNull($this->video->getAuthorFirstName());
        $this->assertNull($this->video->getAuthorLastName());
        $this->assertNull($this->video->getRating());
        $this->assertNull($this->video->isPublic());
        $this->assertNull($this->video->getPublishDate());
        $this->assertNull($this->video->getPublisher());
        $this->assertNull($this->video->getFilepath());
        $this->assertInstanceOf(ArrayCollection::class, $this->video->getFiercePublishers());
        $this->assertCount(0, $this->video->getFiercePublishers());
    }

    public function testSetAndGetTitle(): void
    {
        $this->video->setTitle('Test Video');
        $this->assertSame('Test Video', $this->video->getTitle());
    }

    public function testSetAndGetAuthorFirstName(): void
    {
        $this->video->setAuthorFirstName('John');
        $this->assertSame('John', $this->video->getAuthorFirstName());
    }

    public function testSetAndGetAuthorLastName(): void
    {
        $this->video->setAuthorLastName('Doe');
        $this->assertSame('Doe', $this->video->getAuthorLastName());
    }

    public function testSetAndGetRating(): void
    {
        $this->video->setRating(4);
        $this->assertSame(4, $this->video->getRating());
        $this->video->setRating(null);
        $this->assertNull($this->video->getRating());
    }

    public function testSetAndGetIsPublic(): void
    {
        $this->video->setIsPublic(true);
        $this->assertTrue($this->video->isPublic());
    }

    public function testSetAndGetPublishDate(): void
    {
        $date = new \DateTime();
        $this->video->setPublishDate($date);
        $this->assertSame($date, $this->video->getPublishDate());
        $this->video->setPublishDate(null);
        $this->assertNull($this->video->getPublishDate());
    }

    public function testSetAndGetPublisher(): void
    {
        $this->video->setPublisher('Test Publisher');
        $this->assertSame('Test Publisher', $this->video->getPublisher());
        $this->video->setPublisher(null);
        $this->assertNull($this->video->getPublisher());
    }

    public function testSetAndGetFilepath(): void
    {
        $this->video->setFilepath('/videos/test.mp4');
        $this->assertSame('/videos/test.mp4', $this->video->getFilepath());
        $this->video->setFilepath(null);
        $this->assertNull($this->video->getFilepath());
    }

    public function testAddAndRemoveFiercePublisher(): void
    {
        $publisher = new FiercePublisher();
        $this->video->addFiercePublisher($publisher);
        $this->assertCount(1, $this->video->getFiercePublishers());
        $this->assertTrue($this->video->getFiercePublishers()->contains($publisher));
        $this->assertCount(1, $publisher->getAstonishingVideos());
        $this->assertTrue($publisher->getAstonishingVideos()->contains($this->video));

        $this->video->removeFiercePublisher($publisher);
        $this->assertCount(0, $this->video->getFiercePublishers());
        $this->assertFalse($this->video->getFiercePublishers()->contains($publisher));
        $this->assertCount(0, $publisher->getAstonishingVideos());
        $this->assertFalse($publisher->getAstonishingVideos()->contains($this->video));
    }
}