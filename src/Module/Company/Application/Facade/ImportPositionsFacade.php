<?php

namespace App\Module\Company\Application\Facade;

use App\Module\Company\Application\Command\Position\ImportPositionsCommand;
use App\Module\System\Domain\Enum\Import\ImportKindEnum;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final readonly class ImportPositionsFacade extends AbstractImportFacade
{
    public function import(UploadedFile $file): array
    {
        return $this->handle(
            file: $file,
            folder: 'positions',
            importKind: ImportKindEnum::IMPORT_POSITIONS,
            successMessage: $this->messageService->get('position.import.success', [], 'positions'),
            errorMessage: $this->messageService->get('position.import.error', [], 'positions'),
            importCommand: fn ($import) => $this->commandBus->dispatch(new ImportPositionsCommand($import->getUUID()->toString()))
        );
    }
}
