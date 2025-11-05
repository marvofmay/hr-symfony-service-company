<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\ContractType\Import;

use App\Module\Company\Domain\Entity\ContractType;
use App\Module\Company\Domain\Enum\ContractType\ContractTypeImportColumnEnum;

final class ContractTypeFactory
{
    public function create(array $contractTypeData): ContractType
    {
        $contractType = new ContractType();
        $this->fillData($contractType, $contractTypeData);

        return $contractType;
    }

    public function update(array $contractTypeData, array $existingContractTypes): ContractType
    {
        $contractType = $existingContractTypes[$contractTypeData[ContractTypeImportColumnEnum::CONTRACT_TYPE_NAME->value]];
        $this->fillData($contractType, $contractTypeData);

        return $contractType;
    }

    private function fillData(ContractType $contractType, array $contractTypeData): void
    {
        $name = $contractTypeData[ContractTypeImportColumnEnum::CONTRACT_TYPE_NAME->value] ?? null;
        $description = $contractTypeData[ContractTypeImportColumnEnum::CONTRACT_TYPE_DESCRIPTION->value] ?? null;
        $active = (bool)$contractTypeData[ContractTypeImportColumnEnum::CONTRACT_TYPE_DESCRIPTION->value] ?? true;

        $contractType->setName($name);
        $contractType->setDescription($description);
        $contractType->setActive($active);
    }
}
