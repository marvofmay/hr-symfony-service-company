<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Transformer\Industry;

use App\Module\Company\Domain\Entity\Company;
use App\Module\Company\Domain\Entity\Industry;
use App\Module\Company\Domain\Enum\Industry\IndustryEntityFieldEnum;
use App\Module\Company\Domain\Enum\Industry\IndustryEntityRelationFieldEnum;
use App\Module\Company\Domain\Enum\TimeStampableEntityFieldEnum;
use Doctrine\Common\Collections\Collection;

class IndustryDataTransformer
{
    public function transformToArray(Industry $industry, array $includes = []): array
    {
        $data = [
            IndustryEntityFieldEnum::UUID->value => $industry->getUUID()->toString(),
            IndustryEntityFieldEnum::NAME->value => $industry->getName(),
            IndustryEntityFieldEnum::DESCRIPTION->value => $industry->getDescription(),
            TimeStampableEntityFieldEnum::CREATED_AT->value => $industry->createdAt?->format('Y-m-d H:i:s'),
            TimeStampableEntityFieldEnum::UPDATED_AT->value => $industry->updatedAt?->format('Y-m-d H:i:s'),
            TimeStampableEntityFieldEnum::DELETED_AT->value => $industry->deletedAt?->format('Y-m-d H:i:s'),
        ];

        foreach ($includes as $relation) {
            if (in_array($relation, Industry::getRelations(), true)) {
                $data[$relation] = $this->transformRelation($industry, $relation);
            }
        }

        return $data;
    }

    private function transformRelation(Industry $industry, string $relation): ?array
    {
        return match ($relation) {
            IndustryEntityRelationFieldEnum::COMPANIES->value => $this->transformCompanies($industry->getCompanies()),
            default => null,
        };
    }

    private function transformCompanies(?Collection $companies): ?array
    {
        if (null === $companies || $companies->isEmpty()) {
            return null;
        }

        return array_map(
            fn (Company $company) => [
                Company::COLUMN_UUID => $company->getUUID()->toString(),
                Company::COLUMN_FULL_NAME => $company->getFullName(),
                Company::COLUMN_SHORT_NAME => $company->getShortName(),
                Company::COLUMN_DESCRIPTION => $company->getDescription(),
                Company::COLUMN_NIP => $company->getNIP(),
                Company::COLUMN_REGON => $company->getRegon(),
                Company::COLUMN_INTERNAL_CODE => $company->getInternalCode(),
            ],
            $companies->toArray()
        );
    }
}
