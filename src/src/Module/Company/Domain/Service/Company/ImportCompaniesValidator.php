<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Company;

use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;
use App\Module\System\Domain\Entity\Import;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class ImportCompaniesValidator
{
    public function __construct(
        private TranslatorInterface $translator,
        private CompanyReaderInterface $companyReaderRepository,
        private IndustryReaderInterface $industryReaderRepository,
        private CacheInterface $cache,
    ) {
    }

    public function validate(Import $import): array
    {
        $importer = new ImportCompaniesFromXLSX(
            sprintf('%s/%s', $import->getFile()->getFilePath(), $import->getFile()->getFileName()),
            $this->translator,
            $this->companyReaderRepository,
            $this->industryReaderRepository,
            $this->cache,
        );

        return $importer->validateBeforeImport();
    }
}
