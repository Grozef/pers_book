<?php
declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\WonderfullBook;
use App\Entity\FiercePublisher;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class WonderfullBookTest extends TestCase
{
    private WonderfullBook $book;

    protected function setUp(): void
    {
        $this->book = new WonderfullBook();
    }

    public function testInitialState(): void
    {
        $this->assertNull($this->book->getId());
        $this->assertNull($this->book->getTitle());
        $this->assertNull($this->book->getAuthorFirstName());
        $this->assertNull($this->book->getAuthorLastName());
        $this->assertNull($this->book->getRating());
        $this->assertNull($this->book->isPublic());
        $this->assertNull($this->book->getPrice());
        $this->assertNull($this->book->getPublishedDate());
        $this->assertNull($this->book->getGenre());
        $this->assertNull($this->book->getPublisher());
        $this->assertNull($this->book->getIsbn());
        $this->assertInstanceOf(ArrayCollection::class, $this->book->getFiercePublishers());
        $this->assertCount(0, $this->book->getFiercePublishers());
    }

    public function testSetAndGetTitle(): void
    {
        $this->book->setTitle('Test Book');
        $this->assertSame('Test Book', $this->book->getTitle());
    }

    public function testSetAndGetAuthorFirstName(): void
    {
        $this->book->setAuthorFirstName('Mark');
        $this->assertSame('Mark', $this->book->getAuthorFirstName());
    }

    public function testSetAndGetAuthorLastName(): void
    {
        $this->book->setAuthorLastName('Twain');
        $this->assertSame('Twain', $this->book->getAuthorLastName());
    }

    public function testSetAndGetRating(): void
    {
        $this->book->setRating(5);
        $this->assertSame(5, $this->book->getRating());
        $this->book->setRating(null);
        $this->assertNull($this->book->getRating());
    }

    public function testSetAndGetIsPublic(): void
    {
        $this->book->setIsPublic(true);
        $this->assertTrue($this->book->isPublic());
    }

    public function testSetAndGetPrice(): void
    {
        $this->book->setPrice(29.99);
        $this->assertSame(29.99, $this->book->getPrice());
        $this->book->setPrice(null);
        $this->assertNull($this->book->getPrice());
    }

    public function testSetAndGetPublishedDate(): void
    {
        $date = new \DateTime();
        $this->book->setPublishedDate($date);
        $this->assertSame($date, $this->book->getPublishedDate());
        $this->book->setPublishedDate(null);
        $this->assertNull($this->book->getPublishedDate());
    }

    public function testSetAndGetGenre(): void
    {
        $this->book->setGenre('Fiction');
        $this->assertSame('Fiction', $this->book->getGenre());
        $this->book->setGenre(null);
        $this->assertNull($this->book->getGenre());
    }

    public function testSetAndGetPublisher(): void
    {
        $this->book->setPublisher('Test Publisher');
        $this->assertSame('Test Publisher', $this->book->getPublisher());
        $this->book->setPublisher(null);
        $this->assertNull($this->book->getPublisher());
    }

    public function testSetAndGetIsbn(): void
    {
        $this->book->setIsbn('9781234567890');
        $this->assertSame('9781234567890', $this->book->getIsbn());
        $this->book->setIsbn(null);
        $this->assertNull($this->book->getIsbn());
    }

    public function testAddAndRemoveFiercePublisher(): void
    {
        $publisher = new FiercePublisher();
        $this->book->addFiercePublisher($publisher);
        $this->assertCount(1, $this->book->getFiercePublishers());
        $this->assertTrue($this->book->getFiercePublishers()->contains($publisher));
        $this->assertCount(1, $publisher->getWonderfullBooks());
        $this->assertTrue($publisher->getWonderfullBooks()->contains($this->book));

        $this->book->removeFiercePublisher($publisher);
        $this->assertCount(0, $this->book->getFiercePublishers());
        $this->assertFalse($this->book->getFiercePublishers()->contains($publisher));
        $this->assertCount(0, $publisher->getWonderfullBooks());
        $this->assertFalse($publisher->getWonderfullBooks()->contains($this->book));
    }
}