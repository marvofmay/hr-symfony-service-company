<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Service\Import;

use App\Module\System\Domain\Entity\Import;
use App\Module\System\Domain\Enum\Import\ImportStatusEnum;
use App\Module\System\Domain\Interface\Import\ImportWriterInterface;

readonly class ImportUpdater
{
    public function __construct(private ImportWriterInterface $importWriterRepository)
    {
    }

    public function update(Import $import, ImportStatusEnum $importStatusEnum): void
    {
        match ($importStatusEnum) {
            ImportStatusEnum::PENDING => $import->markAsPending(),
            ImportStatusEnum::FAILED => $import->markAsFailed(),
            ImportStatusEnum::DONE => $import->markAsDone(),
        };

        $this->importWriterRepository->saveImportInDB($import);
    }
}
