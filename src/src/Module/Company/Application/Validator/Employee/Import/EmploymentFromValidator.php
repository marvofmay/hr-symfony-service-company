<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Employee\Import;

use App\Common\Domain\Enum\DateFormatEnum;
use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Common\Shared\Utils\DateFormatValidator;
use App\Module\Company\Domain\Service\Employee\ImportEmployeesFromXLSX;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.import_employee_validator')]
class EmploymentFromValidator implements ImportRowValidatorInterface
{
    public function __construct(private MessageService $messageService)
    {
    }

    public function validate(array $row, array $additionalData = []): ?string
    {
        $employmentFrom = $row[ImportEmployeesFromXLSX::COLUMN_EMPLOYMENT_FROM] ?? null;

        if (empty($employmentFrom)) {
            return $this->messageService->get('employee.employmentFrom.required', [], 'employees');
        }

        $errorMessage = DateFormatValidator::validate($employmentFrom, DateFormatEnum::YYYY_MM_DD->value);
        if (null !== $errorMessage) {
            return $this->messageService->get('employee.employmentFrom.'.$errorMessage, [':dateFormat' => DateFormatEnum::YYYY_MM_DD->value], 'employees');
        }

        return null;
    }
}
