<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\ContractType\Import;

use App\Module\Company\Domain\Enum\ContractType\ContractTypeImportColumnEnum;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeReaderInterface;
use App\Module\Company\Domain\Interface\ContractType\Importer\ImportContractTypesReferenceLoaderInterface;

final class ImportContractTypesReferenceLoader implements ImportContractTypesReferenceLoaderInterface
{
    public array $contractTypes = [] {
        get {
            return $this->contractTypes;
        }
    }

    public function __construct(private readonly ContractTypeReaderInterface $contractTypeReaderRepository)
    {
    }

    public function preload(array $rows): void
    {
        $contractTypeNames = [];

        foreach ($rows as $row) {
            if (!empty($row[ContractTypeImportColumnEnum::CONTRACT_TYPE_NAME->value])) {
                $contractTypeNames[] = trim((string) $row[ContractTypeImportColumnEnum::CONTRACT_TYPE_NAME->value]);
            }
        }

        $contractTypeNames = array_unique($contractTypeNames);

        $this->contractTypes = $this->mapByName($this->contractTypeReaderRepository->getContractTypesByNames($contractTypeNames));
    }

    public function mapByName(iterable $contractTypes): array
    {
        $map = [];
        foreach ($contractTypes as $contractType) {
            $map[trim($contractType->getName())] = $contractType;
        }

        return $map;
    }
}
