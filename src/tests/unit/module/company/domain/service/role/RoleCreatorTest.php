<?php

namespace App\tests\unit\module\company\domain\service\role;

use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;
use App\Module\Company\Domain\Service\Role\RoleCreator;
use PHPUnit\Framework\TestCase;

class RoleCreatorTest extends TestCase
{
    public function testItCreatesAndSavesRole(): void
    {
        $name = 'Użytkownik';
        $description = $name . ' ...';

        $writer = $this->createMock(RoleWriterInterface::class);

        $writer
            ->expects($this->once())
            ->method('saveRoleInDB')
            ->with($this->callback(fn (Role $role) => $role->getName() === $name && $role->getDescription() === $description));

        $creator = new RoleCreator($writer);
        $creator->create($name, $description);
    }
}