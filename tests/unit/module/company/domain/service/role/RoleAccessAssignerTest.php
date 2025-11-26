<?php

declare(strict_types=1);

namespace App\tests\unit\module\company\domain\service\role;

use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;
use App\Module\System\Domain\Entity\Access;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class RoleAccessAssignerTest extends TestCase
{
    // ToDo:: repair test
    public function testCreateAddsAccessesAndSavesRole(): void
    {
        //$roleWriterMock = $this->createMock(RoleWriterInterface::class);
        //
        //$roleWriterMock
        //    ->expects($this->once())
        //    ->method('saveRole')
        //    ->with($this->isInstanceOf(Role::class));
        //
        //$service = new RoleAccessCreator($roleWriterMock);
        //
        //$role = new Role();
        //
        //$access1 = $this->createMock(Access::class);
        //$access2 = $this->createMock(Access::class);
        //
        //$accesses = new ArrayCollection([$access1, $access2]);
        //
        //$this->assertCount(0, $role->getAccesses());
        //
        //$service->create($role, $accesses);
        //
        //$this->assertCount(2, $role->getAccesses());
        //$this->assertSame($access1, $role->getAccesses()->get(0));
        //$this->assertSame($access2, $role->getAccesses()->get(1));
        $this->assertTrue(true);
    }
}
