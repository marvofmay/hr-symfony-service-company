<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Employee\Import;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\Enum\EmployeeImportColumnEnum;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.import_employee_validator')]
class RoleUUIDValidator implements ImportRowValidatorInterface
{
    public function __construct(private MessageService $messageService)
    {
    }

    public function validate(array $row, array $additionalData = []): ?string
    {
        $roleUUID = $row[EmployeeImportColumnEnum::ROLE_UUID->value] ?? null;
        if (empty($roleUUID)) {
            return $this->messageService->get('employee.roleUUID.required', [], 'employees');
        }

        $roleExists = isset($additionalData['roles'][$roleUUID]);
        if (!$roleExists) {
            return $this->messageService->get('role.uuid.notExists', [':uuid' => $roleUUID], 'roles');
        }

        return null;
    }
}
