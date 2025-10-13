<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Department\Import;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Module\Company\Domain\Service\Department\ImportDepartmentsFromXLSX;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.import_department_validator')]
class ParentDepartmentInternalCodeValidator implements ImportRowValidatorInterface
{
    public function __construct()
    {
    }

    public function validate(array $row, array $additionalData = []): ?string
    {
        $parentDepartmentInternalCode = (string) $row[ImportDepartmentsFromXLSX::COLUMN_PARENT_DEPARTMENT_INTERNAL_CODE] ?? null;
        if (empty($parentDepartmentInternalCode)) {
            return null;
        }

        return null;
    }
}
