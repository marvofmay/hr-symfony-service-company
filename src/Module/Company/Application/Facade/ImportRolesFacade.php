<?php

namespace App\Module\Company\Application\Facade;

use App\Module\Company\Application\Command\Role\ImportRolesCommand;
use App\Module\System\Domain\Enum\Import\ImportKindEnum;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final readonly class ImportRolesFacade extends AbstractImportFacade
{
    public function import(UploadedFile $file): array
    {
        return $this->handle(
            file: $file,
            folder: 'roles',
            importKind: ImportKindEnum::IMPORT_ROLES,
            successMessage: $this->messageService->get('role.import.success', [], 'roles'),
            errorMessage: $this->messageService->get('role.import.error', [], 'roles'),
            importCommand: fn ($import) => $this->commandBus->dispatch(new ImportRolesCommand($import->getUUID()->toString()))
        );
    }
}
