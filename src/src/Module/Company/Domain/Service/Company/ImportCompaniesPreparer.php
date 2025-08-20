<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Company;

use App\Module\Company\Domain\Aggregate\Company\ValueObject\CompanyUUID;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;

class ImportCompaniesPreparer
{
    public function __construct(
        private CompanyReaderInterface $companyReaderRepository,
    ) {}

    public function prepare(iterable $rows): array
    {
        $nipMap = [];
        $preparedRows = [];

        foreach ($rows as $row) {
            $nip = trim((string) $row[ImportCompaniesFromXLSX::COLUMN_NIP]);

            $existingCompany = $this->companyReaderRepository->getCompanyByNIP($nip);
            $row['_is_company_already_exists_with_nip'] = null !== $existingCompany;

            if (!isset($nipMap[$nip])) {
                $nipMap[$nip] = $existingCompany
                    ? CompanyUUID::fromString($existingCompany->getUUID()->toString())
                    : CompanyUUID::generate();
            }

            $row['_aggregate_uuid'] = $nipMap[$nip]->toString();
            $preparedRows[] = $row;
        }

        return [$preparedRows, $nipMap];
    }
}