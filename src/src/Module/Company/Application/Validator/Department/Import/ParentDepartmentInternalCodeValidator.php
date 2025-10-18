<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Department\Import;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Module\Company\Domain\Enum\DepartmentImportColumnEnum;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.import_department_validator')]
class ParentDepartmentInternalCodeValidator implements ImportRowValidatorInterface
{
    public function __construct()
    {
    }

    public function validate(array $row, array $additionalData = []): ?string
    {
        $parentDepartmentInternalCode = (string) $row[DepartmentImportColumnEnum::PARENT_DEPARTMENT_INTERNAL_CODE->value] ?? null;
        if (empty($parentDepartmentInternalCode)) {
            return null;
        }

        return null;
    }
}
