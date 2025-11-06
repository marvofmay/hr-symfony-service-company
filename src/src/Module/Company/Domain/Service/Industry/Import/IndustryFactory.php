<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Industry\Import;

use App\Module\Company\Domain\Entity\Industry;
use App\Module\Company\Domain\Enum\Industry\IndustryImportColumnEnum;

final class IndustryFactory
{
    public function create(array $data): Industry
    {
        return Industry::create(
            trim((string)($data[IndustryImportColumnEnum::INDUSTRY_NAME->value] ?? '')),
            $data[IndustryImportColumnEnum::INDUSTRY_DESCRIPTION->value] ?? null
        );
    }

    public function update(Industry $industry, array $data): Industry
    {
        $name = trim((string)($data[IndustryImportColumnEnum::INDUSTRY_NAME->value] ?? ''));
        $description = $data[IndustryImportColumnEnum::INDUSTRY_DESCRIPTION->value] ?? null;

        $industry->rename($name);
        $industry->updateDescription($description);

        return $industry;
    }
}
