<?php

namespace App\Module\Company\Application\Facade;

use App\Module\Company\Application\Command\Industry\ImportIndustriesCommand;
use App\Module\System\Domain\Enum\Import\ImportKindEnum;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final readonly class ImportIndustriesFacade extends AbstractImportFacade
{
    public function import(UploadedFile $file): array
    {
        return $this->handle(
            file: $file,
            folder: 'industries',
            importKind: ImportKindEnum::IMPORT_INDUSTRIES,
            successMessage: $this->messageService->get('industry.import.success', [], 'industries'),
            errorMessage: $this->messageService->get('industry.import.error', [], 'industries'),
            importCommand: fn ($import) => $this->commandBus->dispatch(new ImportIndustriesCommand($import->getUUID()->toString()))
        );
    }
}
