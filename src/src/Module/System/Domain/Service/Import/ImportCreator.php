<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Service\Import;

use App\Module\System\Domain\Entity\Import;
use App\Module\System\Domain\Interface\Import\ImportWriterInterface;

readonly class ImportCreator
{
    public function __construct(private ImportWriterInterface $importWriterRepository)
    {
    }

    public function create(Import $import): void
    {
        $this->importWriterRepository->saveImportInDB($import);
    }
}