<?php

declare(strict_types=1);

namespace App\tests\unit\module\company\domain\service\role;

use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;
use App\Module\Company\Domain\Service\Role\RoleUpdater;
use PHPUnit\Framework\TestCase;

class RoleUpdaterTest extends TestCase
{
    public function testItUpdatesAndSavesRole(): void
    {
        $name = 'Updated Name';
        $description = 'Updated Description';

        $role = new Role();
        $role->setName('Old Name');
        $role->setDescription('Old Description');
        $role->setUpdatedAt();

        $writer = $this->createMock(RoleWriterInterface::class);

        $writer
            ->expects($this->once())
            ->method('saveRoleInDB')
            ->with(
                $this->callback(
                    fn (Role $updatedRole) => $updatedRole->getName() === $name && $updatedRole->getDescription() === $description && $updatedRole->updatedAt instanceof \DateTimeInterface
                )
            );

        $updater = new RoleUpdater($writer);
        $updater->update($role, $name, $description);
    }
}
