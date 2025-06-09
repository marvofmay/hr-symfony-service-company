<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Department;

use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\System\Domain\Entity\Import;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class ImportDepartmentsValidator
{
    public function __construct(
        private TranslatorInterface       $translator,
        private CompanyReaderInterface    $companyReaderRepository,
        private DepartmentReaderInterface $departmentReaderRepository,
        private CacheInterface   $cache,
    )
    {
    }

    public function validate(Import $import): array
    {
        $importer = new ImportDepartmentsFromXLSX(
            sprintf('%s/%s', $import->getFile()->getFilePath(), $import->getFile()->getFileName()),
            $this->translator,
            $this->companyReaderRepository,
            $this->departmentReaderRepository,
            $this->cache,
        );

        return $importer->validateBeforeImport();
    }
}