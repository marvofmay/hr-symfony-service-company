<?php

declare(strict_types=1);

namespace App\tests\unit\module\company\entity\role;

use App\Module\Company\Domain\Entity\Role;
use DateTime;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Guid\Guid;
use Ramsey\Uuid\UuidInterface;

class RoleTest extends TestCase
{
    private Role $role;

    protected function setUp(): void
    {
        $this->role = new Role();
    }

    public function testGettersAndSetters(): void
    {
        $uuid = Guid::uuid4();
        $this->role->setUuid($uuid);
        $this->role->setName("Administrator");
        $this->role->setDescription("Admin role");
        $createdAt = new DateTime();
        $this->role->setCreatedAt($createdAt);
        $this->role->setUpdatedAt($createdAt);
        $this->role->setDeletedAt($createdAt);

        $this->assertEquals($uuid, $this->role->getUuid());
        $this->assertEquals("Administrator", $this->role->getName());
        $this->assertEquals("Admin role", $this->role->getDescription());
        $this->assertEquals($createdAt, $this->role->getCreatedAt());
        $this->assertEquals($createdAt, $this->role->getUpdatedAt());
        $this->assertEquals($createdAt, $this->role->getDeletedAt());
    }

    public function testSetCreatedAtValue(): void
    {
        $this->role->setCreatedAtValue();

        $this->assertInstanceOf(DateTimeInterface::class, $this->role->getCreatedAt());
    }

    public function testGetAttributes(): void
    {
        $attributes = Role::getAttributes();

        $this->assertContains('uuid', $attributes);
        $this->assertContains('name', $attributes);
        $this->assertContains('description', $attributes);
        $this->assertContains('createdAt', $attributes);
        $this->assertContains('updatedAt', $attributes);
        $this->assertContains('deletedAt', $attributes);
    }

    public function testUuidValidation(): void
    {
        $uuid = Guid::uuid4();
        $this->role->setUuid($uuid);

        $this->assertInstanceOf(UuidInterface::class, $this->role->getUuid());
    }
}
