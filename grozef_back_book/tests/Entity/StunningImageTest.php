<?php
declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\StunningImage;
use App\Entity\FiercePublisher;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class StunningImageTest extends TestCase
{
    private StunningImage $image;

    protected function setUp(): void
    {
        $this->image = new StunningImage();
    }

    public function testInitialState(): void
    {
        $this->assertNull($this->image->getId());
        $this->assertNull($this->image->getTitle());
        $this->assertNull($this->image->getAuthorFirstName());
        $this->assertNull($this->image->getAuthorLastName());
        $this->assertNull($this->image->getRating());
        $this->assertNull($this->image->isPublic());
        $this->assertNull($this->image->getPrice());
        $this->assertNull($this->image->getPublishedDate());
        $this->assertNull($this->image->getPublisher());
        $this->assertNull($this->image->getFilepath());
        $this->assertInstanceOf(ArrayCollection::class, $this->image->getFiercePublishers());
        $this->assertCount(0, $this->image->getFiercePublishers());
    }

    public function testSetAndGetTitle(): void
    {
        $this->image->setTitle('Test Image');
        $this->assertSame('Test Image', $this->image->getTitle());
    }

    public function testSetAndGetAuthorFirstName(): void
    {
        $this->image->setAuthorFirstName('Jane');
        $this->assertSame('Jane', $this->image->getAuthorFirstName());
    }

    public function testSetAndGetAuthorLastName(): void
    {
        $this->image->setAuthorLastName('Smith');
        $this->assertSame('Smith', $this->image->getAuthorLastName());
    }

    public function testSetAndGetRating(): void
    {
        $this->image->setRating(3);
        $this->assertSame(3, $this->image->getRating());
        $this->image->setRating(null);
        $this->assertNull($this->image->getRating());
    }

    public function testSetAndGetIsPublic(): void
    {
        $this->image->setIsPublic(false);
        $this->assertFalse($this->image->isPublic());
    }

    public function testSetAndGetPrice(): void
    {
        $this->image->setPrice(19.99);
        $this->assertSame(19.99, $this->image->getPrice());
        $this->image->setPrice(null);
        $this->assertNull($this->image->getPrice());
    }

    public function testSetAndGetPublishedDate(): void
    {
        $date = new \DateTime();
        $this->image->setPublishedDate($date);
        $this->assertSame($date, $this->image->getPublishedDate());
        $this->image->setPublishedDate(null);
        $this->assertNull($this->image->getPublishedDate());
    }

    public function testSetAndGetPublisher(): void
    {
        $this->image->setPublisher('Test Publisher');
        $this->assertSame('Test Publisher', $this->image->getPublisher());
        $this->image->setPublisher(null);
        $this->assertNull($this->image->getPublisher());
    }

    public function testSetAndGetFilepath(): void
    {
        $this->image->setFilepath('/images/test.jpg');
        $this->assertSame('/images/test.jpg', $this->image->getFilepath());
        $this->image->setFilepath(null);
        $this->assertNull($this->image->getFilepath());
    }

    public function testAddAndRemoveFiercePublisher(): void
    {
        $publisher = new FiercePublisher();
        $this->image->addFiercePublisher($publisher);
        $this->assertCount(1, $this->image->getFiercePublishers());
        $this->assertTrue($this->image->getFiercePublishers()->contains($publisher));
        $this->assertCount(1, $publisher->getStunningImages());
        $this->assertTrue($publisher->getStunningImages()->contains($this->image));

        $this->image->removeFiercePublisher($publisher);
        $this->assertCount(0, $this->image->getFiercePublishers());
        $this->assertFalse($this->image->getFiercePublishers()->contains($publisher));
        $this->assertCount(0, $publisher->getStunningImages());
        $this->assertFalse($publisher->getStunningImages()->contains($this->image));
    }
}