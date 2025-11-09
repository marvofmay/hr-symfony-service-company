<?php

declare(strict_types=1);

namespace App\tests\unit\module\company\domain\service\role;

use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleAccessPermissionUpdaterInterface;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;
use App\Module\Company\Domain\Service\Role\RoleAccessPermissionAssigner;
use App\Module\System\Domain\Entity\Access;
use PHPUnit\Framework\TestCase;

class RoleAccessPermissionAssignerTest extends TestCase
{
    public function testCreateAddsAccessesAndSavesRole(): void
    {
        $role = $this->createMock(Role::class);
        $access = $this->createMock(Access::class);
        $permissionsUUIDs = ['uuid-1', 'uuid-2'];

        $updater = $this->createMock(RoleAccessPermissionUpdaterInterface::class);
        $writer = $this->createMock(RoleWriterInterface::class);

        $updater->expects($this->once())
            ->method('updateAccessesPermission')
            ->with($role, $access, $permissionsUUIDs);

        $writer->expects($this->once())
            ->method('saveRole')
            ->with($role);

        $assigner = new RoleAccessPermissionAssigner($writer, $updater);

        $assigner->assign($role, $access, $permissionsUUIDs);
    }
}
