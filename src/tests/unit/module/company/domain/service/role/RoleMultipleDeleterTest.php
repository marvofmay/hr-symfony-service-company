<?php

declare(strict_types=1);

namespace App\tests\unit\module\company\domain\service\role;

use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;
use App\Module\Company\Domain\Service\Role\RoleMultipleDeleter;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class RoleMultipleDeleterTest extends TestCase
{
    public function testMultipleDeleteCallsRepository(): void
    {
        $roles = new ArrayCollection([
            $this->createMock(Role::class),
            $this->createMock(Role::class),
        ]);

        $writer = $this->createMock(RoleWriterInterface::class);

        $writer
            ->expects($this->once())
            ->method('deleteMultipleRolesInDB')
            ->with($this->equalTo($roles));

        $deleter = new RoleMultipleDeleter($writer);

        $deleter->multipleDelete($roles);
    }
}