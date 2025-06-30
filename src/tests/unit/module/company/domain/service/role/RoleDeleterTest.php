<?php

declare(strict_types=1);

namespace App\tests\unit\module\company\domain\service\role;

use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;
use App\Module\Company\Domain\Service\Role\RoleDeleter;
use PHPUnit\Framework\TestCase;

class RoleDeleterTest extends TestCase
{
    public function testItDeletesRole(): void
    {
        $role = new Role();

        $writer = $this->createMock(RoleWriterInterface::class);
        $writer
            ->expects($this->once())
            ->method('deleteRoleInDB')
            ->with($role);

        $deleter = new RoleDeleter($writer);
        $deleter->delete($role);
    }
}