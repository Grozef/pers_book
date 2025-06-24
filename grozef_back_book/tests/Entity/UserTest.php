<?php
declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\UserInfo;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User();
    }

    public function testInitialState(): void
    {
        $this->assertNull($this->user->getId());
        $this->assertNull($this->user->getMail());
        $this->assertNull($this->user->getPassword());
        $this->assertNull($this->user->isActive());
        $this->assertNull($this->user->getUserInfo());
    }

    public function testSetAndGetMail(): void
    {
        $this->user->setMail('test@example.com');
        $this->assertSame('test@example.com', $this->user->getMail());
    }

    public function testSetAndGetPassword(): void
    {
        $this->user->setPassword('secret123');
        $this->assertSame('secret123', $this->user->getPassword());
    }

    public function testSetAndGetIsActive(): void
    {
        $this->user->setIsActive(true);
        $this->assertTrue($this->user->isActive());
    }

    public function testSetAndGetUserInfo(): void
    {
        $userInfo = new UserInfo();
        $this->user->setUserInfo($userInfo);
        $this->assertSame($userInfo, $this->user->getUserInfo());
    }
}