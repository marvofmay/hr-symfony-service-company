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
            trim($data[ContractTypeImportColumnEnum::CONTRACT_TYPE_NAME->value] ?? ''),
            !is_null($data[ContractTypeImportColumnEnum::CONTRACT_TYPE_DESCRIPTION->value]) ? trim($data[ContractTypeImportColumnEnum::CONTRACT_TYPE_DESCRIPTION->value]) : null,
            (bool)$data[ContractTypeImportColumnEnum::CONTRACT_TYPE_ACTIVE->value]
        );
    }

    public function update(ContractType $contractType, array $data): ContractType
    {
        $name = trim($data[ContractTypeImportColumnEnum::CONTRACT_TYPE_NAME->value] ?? '');
        $description = $data[ContractTypeImportColumnEnum::CONTRACT_TYPE_DESCRIPTION->value] ?? null;
        $active = (bool)$data[ContractTypeImportColumnEnum::CONTRACT_TYPE_ACTIVE->value];

        $contractType->rename($name);
        if (null !== $description) {
            $contractType->updateDescription(trim($description));
        }
        if ($active) {
            $contractType->activate();
        } else {
            $contractType->deactivate();
        }

        return $contractType;
    }
}
