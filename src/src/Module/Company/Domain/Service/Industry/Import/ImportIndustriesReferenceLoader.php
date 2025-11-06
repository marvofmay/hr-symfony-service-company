<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Industry\Import;

use App\Module\Company\Domain\Enum\Industry\IndustryImportColumnEnum;
use App\Module\Company\Domain\Interface\Industry\Importer\ImportIndustriesReferenceLoaderInterface;
use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;

final class ImportIndustriesReferenceLoader implements ImportIndustriesReferenceLoaderInterface
{
    public array $industries = [] {
        get {
            return $this->industries;
        }
    }

    public function __construct(private readonly IndustryReaderInterface $industryReaderRepository)
    {
    }

    public function preload(array $rows): void
    {
        $industryNames = [];

        foreach ($rows as $row) {
            if (!empty($row[IndustryImportColumnEnum::INDUSTRY_NAME->value])) {
                $industryNames[] = trim((string) $row[IndustryImportColumnEnum::INDUSTRY_NAME->value]);
            }
        }

        $industryNames = array_unique($industryNames);

        $this->industries = $this->mapByName($this->industryReaderRepository->getIndustriesByNames($industryNames));
    }

    public function mapByName(iterable $industries): array
    {
        $map = [];
        foreach ($industries as $industry) {
            $map[trim($industry->getName())] = $industry;
        }

        return $map;
    }
}
