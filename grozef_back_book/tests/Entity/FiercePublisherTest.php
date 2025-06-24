<?php
declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\AstonishingVideo;
use App\Entity\FiercePublisher;
use App\Entity\StunningImage;
use App\Entity\WonderfullBook;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class FiercePublisherTest extends TestCase
{
    private FiercePublisher $publisher;

    protected function setUp(): void
    {
        $this->publisher = new FiercePublisher();
    }

    public function testInitialState(): void
    {
        $this->assertNull($this->publisher->getId());
        $this->assertNull($this->publisher->getName());
        $this->assertNull($this->publisher->getAddress());
        $this->assertNull($this->publisher->getTel());
        $this->assertNull($this->publisher->getMail());
        $this->assertNull($this->publisher->getPostalCode());
        $this->assertNull($this->publisher->getCountry());
        $this->assertInstanceOf(ArrayCollection::class, $this->publisher->getAstonishingVideos());
        $this->assertCount(0, $this->publisher->getAstonishingVideos());
        $this->assertInstanceOf(ArrayCollection::class, $this->publisher->getStunningImages());
        $this->assertCount(0, $this->publisher->getStunningImages());
        $this->assertInstanceOf(ArrayCollection::class, $this->publisher->getWonderfullBooks());
        $this->assertCount(0, $this->publisher->getWonderfullBooks());
    }

    public function testSetAndGetName(): void
    {
        $this->publisher->setName('Test Publisher');
        $this->assertSame('Test Publisher', $this->publisher->getName());
    }

    public function testSetAndGetAddress(): void
    {
        $this->publisher->setAddress('123 Test St');
        $this->assertSame('123 Test St', $this->publisher->getAddress());
        $this->publisher->setAddress(null);
        $this->assertNull($this->publisher->getAddress());
    }

    public function testSetAndGetTel(): void
    {
        $this->publisher->setTel('+123456789');
        $this->assertSame('+123456789', $this->publisher->getTel());
        $this->publisher->setTel(null);
        $this->assertNull($this->publisher->getTel());
    }

    public function testSetAndGetMail(): void
    {
        $this->publisher->setMail('test@example.com');
        $this->assertSame('test@example.com', $this->publisher->getMail());
        $this->publisher->setMail(null);
        $this->assertNull($this->publisher->getMail());
    }

    public function testSetAndGetPostalCode(): void
    {
        $this->publisher->setPostalCode('12345');
        $this->assertSame('12345', $this->publisher->getPostalCode());
        $this->publisher->setPostalCode(null);
        $this->assertNull($this->publisher->getPostalCode());
    }

    public function testSetAndGetCountry(): void
    {
        $this->publisher->setCountry('France');
        $this->assertSame('France', $this->publisher->getCountry());
        $this->publisher->setCountry(null);
        $this->assertNull($this->publisher->getCountry());
    }

    public function testAddAndRemoveAstonishingVideo(): void
    {
        $video = new AstonishingVideo();
        $this->publisher->addAstonishingVideo($video);
        $this->assertCount(1, $this->publisher->getAstonishingVideos());
        $this->assertTrue($this->publisher->getAstonishingVideos()->contains($video));
        $this->publisher->removeAstonishingVideo($video);
        $this->assertCount(0, $this->publisher->getAstonishingVideos());
        $this->assertFalse($this->publisher->getAstonishingVideos()->contains($video));
    }

    public function testAddAndRemoveStunningImage(): void
    {
        $image = new StunningImage();
        $this->publisher->addStunningImage($image);
        $this->assertCount(1, $this->publisher->getStunningImages());
        $this->assertTrue($this->publisher->getStunningImages()->contains($image));
        $this->publisher->removeStunningImage($image);
        $this->assertCount(0, $this->publisher->getStunningImages());
        $this->assertFalse($this->publisher->getStunningImages()->contains($image));
    }

    public function testAddAndRemoveWonderfullBook(): void
    {
        $book = new WonderfullBook();
        $this->publisher->addWonderfullBook($book);
        $this->assertCount(1, $this->publisher->getWonderfullBooks());
        $this->assertTrue($this->publisher->getWonderfullBooks()->contains($book));
        $this->publisher->removeWonderfullBook($book);
        $this->assertCount(0, $this->publisher->getWonderfullBooks());
        $this->assertFalse($this->publisher->getWonderfullBooks()->contains($book));
    }
}