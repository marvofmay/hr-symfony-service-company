<?php

declare(strict_types=1);

namespace App\tests\unit\module\company\domain\service\role;

use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use App\Module\Company\Domain\Service\Role\ImportRolesFromXLSX;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImportRolesFromXLSXTest extends TestCase
{
    private TranslatorInterface $translator;
    private RoleReaderInterface $reader;

    protected function setUp(): void
    {
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->reader = $this->createMock(RoleReaderInterface::class);

        $this->translator
            ->method('trans')
            ->willReturnCallback(fn ($key, $params = [], $domain = null) => $key);
    }

    public function testValidateRowReturnsErrorWhenNameIsEmpty(): void
    {
        $importer = $this->getImporter();

        $errors = $importer->validateRow([''], 1);

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('role.name.required', $errors[0]);
    }

    public function testValidateRowReturnsErrorWhenNameIsTooShort(): void
    {
        $importer = $this->getImporter();

        $errors = $importer->validateRow(['ab'], 1);

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('role.name.minimumLength', $errors[0]);
    }

    public function testValidateRowReturnsErrorWhenRoleAlreadyExists(): void
    {
        $this->reader
            ->method('isRoleExists')
            ->willReturn(true);

        $importer = $this->getImporter();

        $errors = $importer->validateRow(['ExistingRole'], 1);

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('role.name.alreadyExists', $errors[0]);
    }

    public function testValidateRowReturnsEmptyWhenRoleIsValidAndDoesNotExist(): void
    {
        $this->reader
            ->method('isRoleExists')
            ->willReturn(false);

        $importer = $this->getImporter();

        $errors = $importer->validateRow(['ValidRole'], 1);

        $this->assertEmpty($errors);
    }

    private function getImporter(): ImportRolesFromXLSX
    {
        $importer = new class($this->translator, $this->reader) extends ImportRolesFromXLSX {
            public array $errors = [];
        };

        $importer->setFilePath('fake/path.xlsx');

        return $importer;
    }
}
