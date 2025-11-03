<?php

declare(strict_types=1);

namespace App\tests\integration\module\company\domain\role\service;

use App\Common\Domain\Enum\DeleteTypeEnum;
use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;
use App\Module\Company\Domain\Service\Role\AccessPermissionSynchronizer;
use App\Module\Company\Domain\Service\Role\RoleAccessPermissionAssigner;
use App\Module\Company\Domain\Service\Role\RoleAccessPermissionUpdater;
use App\Module\System\Domain\Entity\Access;
use App\Module\System\Domain\Entity\Permission;
use App\Module\System\Domain\Interface\Permission\PermissionReaderInterface;
use App\Module\System\Domain\Interface\RoleAccessPermission\RoleAccessPermissionWriterInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class RoleAccessPermissionAssignerTest extends TestCase
{
    public function testAssignUpdatesAccessPermissionsAndSavesRole(): void
    {
        $roleWriter = $this->createMock(RoleWriterInterface::class);
        $permissionReader = $this->createMock(PermissionReaderInterface::class);
        $roleAccessPermissionWriter = $this->createMock(RoleAccessPermissionWriterInterface::class);

        $role = $this->createMock(Role::class);
        $access = $this->createMock(Access::class);

        $perm1 = $this->createMock(Permission::class);
        $perm2 = $this->createMock(Permission::class);
        $perm3 = $this->createMock(Permission::class);

        $perm1->method('getUUID')->willReturn(Uuid::fromString('00000000-0000-0000-0000-000000000001'));
        $perm2->method('getUUID')->willReturn(Uuid::fromString('00000000-0000-0000-0000-000000000002'));
        $perm3->method('getUUID')->willReturn(Uuid::fromString('00000000-0000-0000-0000-000000000003'));

        $permissionReader->expects($this->once())
            ->method('getPermissions')
            ->willReturn(new ArrayCollection([$perm1, $perm2, $perm3]));

        $access->method('getPermissions')->willReturn(new ArrayCollection([$perm1, $perm3]));

        $payloadUUIDs = [
            '00000000-0000-0000-0000-000000000001',
            '00000000-0000-0000-0000-000000000002',
        ];

        $roleAccessPermissionWriter->expects($this->once())
            ->method('deleteRoleAccessPermissionsInDB')
            ->with(
                $role,
                $access,
                $perm3,
                DeleteTypeEnum::HARD_DELETE
            );

        $role->expects($this->once())
            ->method('removeAccessPermission')
            ->with($access, $perm3);

        $role->expects($this->once())
            ->method('addAccessPermission')
            ->with($access, $perm2);

        $roleWriter->expects($this->once())
            ->method('saveRoleInDB')
            ->with($role);

        $synchronizer = new AccessPermissionSynchronizer($roleAccessPermissionWriter);
        $updater = new RoleAccessPermissionUpdater($permissionReader, $synchronizer);
        $assigner = new RoleAccessPermissionAssigner($roleWriter, $updater);

        $assigner->assign($role, $access, $payloadUUIDs);
    }
}