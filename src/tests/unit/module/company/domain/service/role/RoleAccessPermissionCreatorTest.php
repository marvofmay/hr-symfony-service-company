<?php

declare(strict_types=1);

namespace App\tests\unit\module\company\domain\service\role;

use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;
use App\Module\Company\Domain\Service\Role\RoleAccessPermissionCreator;
use App\Module\System\Domain\Entity\Access;
use App\Module\System\Domain\Entity\Permission;
use App\Module\System\Domain\Interface\Access\AccessReaderInterface;
use App\Module\System\Domain\Interface\Permission\PermissionReaderInterface;
use PHPUnit\Framework\TestCase;

class RoleAccessPermissionCreatorTest extends TestCase
{
    public function testCreateAddsAccessPermissionsAndSavesRole(): void
    {
        $roleWriterMock = $this->createMock(RoleWriterInterface::class);
        $accessReaderMock = $this->createMock(AccessReaderInterface::class);
        $permissionReaderMock = $this->createMock(PermissionReaderInterface::class);

        $accessObj1 = $this->createMock(Access::class);
        $permissionObj1 = $this->createMock(Permission::class);
        $permissionObj2 = $this->createMock(Permission::class);

        $accessesInput = [
            [
                'uuid' => 'access-uuid-1',
                'permissions' => ['permission-uuid-1', 'permission-uuid-2'],
            ],
        ];

        $accessReaderMock->expects($this->once())
            ->method('getAccessByUUID')
            ->with('access-uuid-1')
            ->willReturn($accessObj1);

        $permissionReaderMock->expects($this->exactly(2))
            ->method('getPermissionByUUID')
            ->willReturnMap([
                ['permission-uuid-1', $permissionObj1],
                ['permission-uuid-2', $permissionObj2],
            ]);

        $roleWriterMock->expects($this->once())
            ->method('saveRoleInDB')
            ->with($this->isInstanceOf(Role::class));

        $roleMock = $this->getMockBuilder(Role::class)
            ->onlyMethods(['addAccessPermission'])
            ->getMock();

        $calls = [];
        $roleMock->expects($this->exactly(2))
            ->method('addAccessPermission')
            ->with($this->callback(function ($access) use ($accessObj1) {
                return $access === $accessObj1;
            }), $this->callback(function ($permission) use (&$calls, $permissionObj1, $permissionObj2) {
                $calls[] = $permission;
                return $permission === $permissionObj1 || $permission === $permissionObj2;
            }));

        $service = new RoleAccessPermissionCreator(
            $roleWriterMock,
            $accessReaderMock,
            $permissionReaderMock
        );

        $service->create($roleMock, $accessesInput);

        $this->assertCount(2, $calls);
        $this->assertContains($permissionObj1, $calls);
        $this->assertContains($permissionObj2, $calls);
    }
}
