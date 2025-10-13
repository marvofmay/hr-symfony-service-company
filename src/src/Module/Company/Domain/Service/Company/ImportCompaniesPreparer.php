<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Company;

use App\Common\Infrastructure\Cache\EntityReferenceCache;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\CompanyUUID;
use App\Module\Company\Domain\Entity\Company;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;

final readonly class ImportCompaniesPreparer
{
    public function __construct(
        private CompanyReaderInterface $companyReaderRepository,
        private EntityReferenceCache $entityReferenceCache,
    ) {
    }

    public function prepare(iterable $rows): array
    {
        $nipMap = [];
        $preparedRows = [];

        foreach ($rows as $row) {
            $nip = trim((string) $row[ImportCompaniesFromXLSX::COLUMN_NIP]);
            $existingCompany = $this->entityReferenceCache->get(
                Company::class,
                $nip,
                fn (string $nip) => $this->companyReaderRepository->getCompanyByNIP($nip)
            );

            $row[ImportCompaniesFromXLSX::COLUMN_DYNAMIC_IS_COMPANY_WITH_NIP_ALREADY_EXISTS] = null !== $existingCompany;

            if (!isset($nipMap[$nip])) {
                $nipMap[$nip] = $existingCompany
                    ? CompanyUUID::fromString($existingCompany->getUUID()->toString())
                    : CompanyUUID::generate();
            }

            $row[ImportCompaniesFromXLSX::COLUMN_DYNAMIC_AGGREGATE_UUID] = $nipMap[$nip]->toString();
            $preparedRows[] = $row;
        }

        return [$preparedRows, $nipMap];
    }
}
