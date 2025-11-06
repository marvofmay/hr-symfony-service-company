<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\ContractType\Import;

use App\Module\Company\Domain\Entity\ContractType;
use App\Module\Company\Domain\Enum\ContractType\ContractTypeImportColumnEnum;

final class ContractTypeFactory
{
    public function create(array $data): ContractType
    {
        return ContractType::create(
            $data[ContractTypeImportColumnEnum::CONTRACT_TYPE_NAME->value] ?? null,
            $data[ContractTypeImportColumnEnum::CONTRACT_TYPE_DESCRIPTION->value] ?? null,
            (bool)$data[ContractTypeImportColumnEnum::CONTRACT_TYPE_ACTIVE->value] ?? false
        );
    }

    public function update(ContractType $contractType, array $data): ContractType
    {
        $name = $data[ContractTypeImportColumnEnum::CONTRACT_TYPE_NAME->value] ?? null;
        $description = $data[ContractTypeImportColumnEnum::CONTRACT_TYPE_DESCRIPTION->value] ?? null;
        $active = (bool)$data[ContractTypeImportColumnEnum::CONTRACT_TYPE_ACTIVE->value] ?? false;

        $contractType->rename($name);
        if (null !== $description) {
            $contractType->updateDescription($description);
        }
        if ($active) {
            $contractType->activate();
        } else {
            $contractType->deactivate();
        }

        return $contractType;
    }
}
