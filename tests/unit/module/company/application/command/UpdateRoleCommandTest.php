<?php

declare(strict_types=1);

namespace App\tests\unit\module\company\application\command;

use App\Module\Company\Application\Command\Role\UpdateRoleCommand;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class UpdateRoleCommandTest extends TestCase
{
    public function testItStoresAndReturnsNameDescriptionAndUUID(): void
    {
        $uuid = Uuid::uuid4()->toString();
        $name = 'Moderator';
        $description = 'Opis ....';

        $command = new UpdateRoleCommand($uuid, $name, $description);

        $this->assertSame($uuid, $command->roleUUID);
        $this->assertSame($name, $command->name);
        $this->assertSame($description, $command->description);
    }

    public function testItAllowsNullDescription(): void
    {
        $uuid = Uuid::uuid4()->toString();
        $name = 'Użytkownik';

        $command = new UpdateRoleCommand($uuid, $name, null);

        $this->assertSame($uuid, $command->roleUUID);
        $this->assertSame($name, $command->name);
        $this->assertNull($command->description);
    }

}
