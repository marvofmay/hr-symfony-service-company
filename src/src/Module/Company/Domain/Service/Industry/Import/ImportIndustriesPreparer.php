<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Industry\Import;

use App\Module\Company\Domain\Enum\Industry\IndustryImportColumnEnum;
use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;

final readonly class ImportIndustriesPreparer
{
    public function __construct(
        private IndustryReaderInterface $industryReaderRepository,
    ) {
    }

    public function prepare(iterable $rows, array $existingIndustries): array
    {
        $industryNameMap = [];
        $preparedRows = [];

        foreach ($rows as $row) {
            $name = trim((string) $row[IndustryImportColumnEnum::INDUSTRY_NAME->value]);
            $existingIndustry = $existingIndustries[$name] ?? $this->industryReaderRepository->getIndustryByName($name);

            $row[IndustryImportColumnEnum::DYNAMIC_IS_INDUSTRY_WITH_NAME_ALREADY_EXISTS->value] = null !== $existingIndustry;

            if (!isset($industryNameMap[$name])) {
                $industryNameMap[$name] = $existingIndustry ? $existingIndustry->getUUID()->toString() : null;
            }

            $row[IndustryImportColumnEnum::DYNAMIC_INDUSTRY_UUID->value] = $industryNameMap[$name];
            $preparedRows[] = $row;
        }

        return [$preparedRows, $industryNameMap];
    }
}
