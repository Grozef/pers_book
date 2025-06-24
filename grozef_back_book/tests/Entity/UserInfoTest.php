<?php
declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\UserInfo;
use PHPUnit\Framework\TestCase;

class UserInfoTest extends TestCase
{
    private UserInfo $userInfo;

    protected function setUp(): void
    {
        $this->userInfo = new UserInfo();
    }

    public function testInitialState(): void
    {
        $this->assertNull($this->userInfo->getId());
        $this->assertNull($this->userInfo->getFirstName());
        $this->assertNull($this->userInfo->getLastName());
        $this->assertNull($this->userInfo->getAddress());
        $this->assertNull($this->userInfo->getTel());
        $this->assertNull($this->userInfo->getPostalCode());
        $this->assertNull($this->userInfo->getCountry());
    }

    public function testSetAndGetFirstName(): void
    {
        $this->userInfo->setFirstName('Alice');
        $this->assertSame('Alice', $this->userInfo->getFirstName());
    }

    public function testSetAndGetLastName(): void
    {
        $this->userInfo->setLastName('Wonderland');
        $this->assertSame('Wonderland', $this->userInfo->getLastName());
    }

    public function testSetAndGetAddress(): void
    {
        $this->userInfo->setAddress('456 Dream St');
        $this->assertSame('456 Dream St', $this->userInfo->getAddress());
        $this->userInfo->setAddress(null);
        $this->assertNull($this->userInfo->getAddress());
    }

    public function testSetAndGetTel(): void
    {
        $this->userInfo->setTel('+987654321');
        $this->assertSame('+987654321', $this->userInfo->getTel());
        $this->userInfo->setTel(null);
        $this->assertNull($this->userInfo->getTel());
    }

    public function testSetAndGetPostalCode(): void
    {
        $this->userInfo->setPostalCode('67890');
        $this->assertSame('67890', $this->userInfo->getPostalCode());
        $this->userInfo->setPostalCode(null);
        $this->assertNull($this->userInfo->getPostalCode());
    }

    public function testSetAndGetCountry(): void
    {
        $this->userInfo->setCountry('Wonderland');
        $this->assertSame('Wonderland', $this->userInfo->getCountry());
        $this->userInfo->setCountry(null);
        $this->assertNull($this->userInfo->getCountry());
    }
}