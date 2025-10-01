<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Employee;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\Service\Employee\ImportEmployeesFromXLSX;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.import_employees_validator')]
class LastNameValidator implements ImportRowValidatorInterface
{
    public function __construct(private MessageService $messageService) {}

    public function validate(array $row, array $additionalData = []): ?string
    {
        $lastName = $row[ImportEmployeesFromXLSX::COLUMN_LAST_NAME] ?? null;

        if (empty($lastName)) {
            return $this->messageService->get('employee.lastName.required', [], 'employees');
        }

        if (strlen($lastName) < 3) {
            return $this->messageService->get('employee.lastName.minimumLength', [':qty' => 3], 'employees');
        }

        return null;
    }
}