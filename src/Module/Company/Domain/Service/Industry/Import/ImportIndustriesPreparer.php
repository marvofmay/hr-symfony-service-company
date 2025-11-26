<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Industry\Import;

use App\Module\Company\Domain\Enum\Industry\IndustryImportColumnEnum;

final readonly class ImportIndustriesPreparer
{
    public function prepare(iterable $rows, array $existingIndustries): array
    {
        $preparedRows = [];
        foreach ($rows as $row) {
            $name = trim((string) $row[IndustryImportColumnEnum::INDUSTRY_NAME->value]);
            $row[IndustryImportColumnEnum::DYNAMIC_IS_INDUSTRY_WITH_NAME_ALREADY_EXISTS->value] = $existingIndustries[$name] ?? false;
            $preparedRows[] = $row;
        }

        return $preparedRows;
    }
}
