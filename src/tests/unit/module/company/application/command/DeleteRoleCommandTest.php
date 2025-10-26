<?php

namespace App\tests\unit\module\company\application\command;

use App\Module\Company\Application\Command\Role\DeleteRoleCommand;
use App\Module\Company\Domain\Entity\Role;
use PHPUnit\Framework\TestCase;

class DeleteRoleCommandTest extends TestCase
{
    public function testItReturnsTheRole(): void
    {
        $roleMock = $this->createMock(Role::class);

        $command = new DeleteRoleCommand($roleMock);

        $this->assertSame($roleMock, $command->getRole());
    }
}
