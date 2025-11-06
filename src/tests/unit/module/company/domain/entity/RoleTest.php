<?php

declare(strict_types=1);

namespace App\tests\unit\module\company\domain\entity;

use App\Module\Company\Domain\Entity\Role;
use PHPUnit\Framework\TestCase;;

class RoleTest extends TestCase
{
    private Role $role;

    protected function setUp(): void
    {
        $this->role = Role::create('New role', 'New role description');
    }

    public function testGettersAndSetters(): void
    {
        $createdAt = new \DateTime();
        $this->role->setCreatedAt();
        $this->role->setUpdatedAt();
        $this->role->setDeletedAt($createdAt);

        $this->assertEquals('New role', $this->role->getName());
        $this->assertEquals('New role description', $this->role->getDescription());
        $this->assertEquals($createdAt, $this->role->getDeletedAt());
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

}
