<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Service\Import;

use App\Module\Company\Domain\Entity\Employee;
use App\Module\System\Domain\Entity\File;
use App\Module\System\Domain\Entity\Import;
use App\Module\System\Domain\Enum\Import\ImportKindEnum;
use App\Module\System\Domain\Enum\Import\ImportStatusEnum;
use App\Module\System\Domain\Interface\Import\ImportWriterInterface;

readonly class ImportCreator
{
    public function __construct(private ImportWriterInterface $importWriterRepository)
    {
    }

    public function create(ImportKindEnum $kindEnum, ImportStatusEnum $statusEnum, File $file, ?Employee $employee = null): void
    {
        $import = new Import();
        $import->setKind($kindEnum);

        match ($statusEnum) {
            ImportStatusEnum::PENDING => $import->markAsPending(),
            ImportStatusEnum::FAILED => $import->markAsFailed(),
            ImportStatusEnum::DONE => $import->markAsDone(),
        };

        $import->setEmployee($employee);
        $import->setFile($file);

        $this->importWriterRepository->saveImportInDB($import);
    }
}
