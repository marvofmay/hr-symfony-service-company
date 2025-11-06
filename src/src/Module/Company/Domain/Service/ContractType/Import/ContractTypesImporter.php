<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\ContractType\Import;

use App\Module\Company\Domain\Enum\ContractType\ContractTypeImportColumnEnum;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeWriterInterface;
use App\Module\Company\Domain\Interface\ContractType\Importer\ContractTypesImporterInterface;
use Doctrine\Common\Collections\ArrayCollection;

final class ContractTypesImporter implements ContractTypesImporterInterface
{
    private array $contractTypes = [];

    public function __construct(
        private readonly ContractTypeWriterInterface $contractTypeWriterRepository,
        private readonly ContractTypeFactory $contractTypeFactory,
    ) {
    }

    public function save(array $preparedRows, array $existingContractTypes): void
    {
        foreach ($preparedRows as $preparedRow) {
            if (array_key_exists($preparedRow[ContractTypeImportColumnEnum::CONTRACT_TYPE_NAME->value], $existingContractTypes)) {
                $contractType = $this->contractTypeFactory->update(contractTypeData: $preparedRow, existingContractTypes: $existingContractTypes);
            } else {
                $contractType = $this->contractTypeFactory->create(contractTypeData: $preparedRow);
            }
            $this->contractTypes[] = $contractType;
        }

        $this->contractTypeWriterRepository->saveContractTypesInDB(new ArrayCollection($this->contractTypes));
    }
}
