<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Employee\Import;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\Service\Employee\ImportEmployeesFromXLSX;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.import_employee_validator')]
class DepartmentUUIDValidator implements ImportRowValidatorInterface
{
    public function __construct(private MessageService $messageService)
    {
    }

    public function validate(array $row, array $additionalData = []): ?string
    {
        $departmentUUID = $row[ImportEmployeesFromXLSX::COLUMN_DEPARTMENT_UUID] ?? null;
        if (empty($departmentUUID)) {
            return $this->messageService->get('employee.departmentUUID.required', [], 'employees');
        }

        $departmentExists = isset($additionalData['departments'][$departmentUUID]);
        if (!$departmentExists) {
            return $this->messageService->get('department.uuid.notExists', [':uuid' => $departmentUUID], 'departments');
        }

        return null;
    }
}
