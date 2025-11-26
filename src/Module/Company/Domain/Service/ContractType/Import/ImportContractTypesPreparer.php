<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\ContractType\Import;

use App\Module\Company\Domain\Enum\ContractType\ContractTypeImportColumnEnum;

final readonly class ImportContractTypesPreparer
{
    public function prepare(iterable $rows, array $existingContractTypes): array
    {
        $preparedRows = [];
        foreach ($rows as $row) {
            $name = trim((string) $row[ContractTypeImportColumnEnum::CONTRACT_TYPE_NAME->value]);
            $row[ContractTypeImportColumnEnum::DYNAMIC_IS_CONTRACT_TYPE_WITH_NAME_ALREADY_EXISTS->value] = $existingContractTypes[$name] ?? false;
            $preparedRows[] = $row;
        }

        return $preparedRows;
    }
}
