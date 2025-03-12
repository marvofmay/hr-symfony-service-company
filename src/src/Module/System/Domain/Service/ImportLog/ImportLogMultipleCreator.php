<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Service\ImportLog;

use App\Module\System\Domain\Entity\Import;
use App\Module\System\Domain\Entity\ImportLog;
use App\Module\System\Domain\Enum\ImportLogKindEnum;
use App\Module\System\Domain\Interface\ImportLog\ImportLogWriterInterface;
use Doctrine\Common\Collections\ArrayCollection;

readonly class ImportLogMultipleCreator
{
    public function __construct(private ImportLogWriterInterface $importLogWriterRepository)
    {
    }

    public function multipleCreate(Import $import, array $data, ImportLogKindEnum $enum): void
    {
        $importLogs = new ArrayCollection();
        foreach ($data as $item) {
            $importLog = new ImportLog();
            $importLog->setImport($import);
            $importLog->setKind($enum);
            $importLog->setData([$item]);

            $importLogs[] = $importLog;
        }

        $this->importLogWriterRepository->saveImportLogsInDB($importLogs);
    }
}