<?php

declare(strict_types=1);

namespace App\tests\unit\module\company\domain\dto\role;

use App\Module\Company\Domain\DTO\Role\ImportDTO;
use PHPUnit\Framework\TestCase;

class ImportRolesTest extends TestCase
{
    public function testCanCreateWithUuid(): void
    {
        $uuid = 'abc-123';
        $dto = new ImportDTO($uuid);

        $this->assertSame($uuid, $dto->importUUID);
    }

    public function testCanCreateWithNull(): void
    {
        $dto = new ImportDTO(null);

        $this->assertNull($dto->importUUID);
    }
}
