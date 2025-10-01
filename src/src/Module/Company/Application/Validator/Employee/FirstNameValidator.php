<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Employee;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\Service\Employee\ImportEmployeesFromXLSX;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.import_employees_validator')]
class FirstNameValidator implements ImportRowValidatorInterface
{
    public function __construct(private MessageService $messageService) {}

    public function validate(array $row, array $additionalData = []): ?string
    {
        $firstName = $row[ImportEmployeesFromXLSX::COLUMN_FIRST_NAME] ?? null;

        if (empty($firstName)) {
            return $this->messageService->get('employee.firstName.required', [], 'employees');
        }

        if (strlen($firstName) < 3) {
            return $this->messageService->get('employee.firstName.minimumLength', [':qty' => 3], 'employees');
        }

        return null;
    }
}