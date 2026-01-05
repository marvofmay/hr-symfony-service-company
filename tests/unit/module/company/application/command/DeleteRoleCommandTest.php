<?php

namespace App\tests\unit\module\company\application\command;

use App\Module\Company\Application\Command\Role\DeleteRoleCommand;
use PHPUnit\Framework\TestCase;

final class DeleteRoleCommandTest extends TestCase
{
    public function testItStoresRoleUUID(): void
    {
        $uuid = '123e4567-e89b-12d3-a456-426614174000';

        $command = new DeleteRoleCommand($uuid);

        $this->assertSame($uuid, $command->roleUUID);
        $this->assertTrue(defined(DeleteRoleCommand::class . '::ROLE_UUID'));
        $this->assertSame('roleUUID', DeleteRoleCommand::ROLE_UUID);
    }
}
