<?php

declare(strict_types=1);

namespace App\tests\unit\module\company\domain\service\role;

use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;
use App\Module\Company\Domain\Service\Role\RoleUpdater;
use PHPUnit\Framework\TestCase;

final class RoleUpdaterTest extends TestCase
{
    public function testItUpdatesAndSavesRole(): void
    {
        $name = 'Updated Name';
        $description = 'Updated Description';

        $role = Role::create('Old Name', 'Old Description');

        $writer = $this->createMock(RoleWriterInterface::class);
        $writer->expects($this->once())
            ->method('saveRole')
            ->with($this->callback(
                fn (Role $r) =>
                $r->getName() === $name &&
                $r->getDescription() === $description
            ));

        $updater = new RoleUpdater($writer);
        $updater->update($role, $name, $description);

        $this->assertSame($name, $role->getName());
        $this->assertSame($description, $role->getDescription());
    }
}
