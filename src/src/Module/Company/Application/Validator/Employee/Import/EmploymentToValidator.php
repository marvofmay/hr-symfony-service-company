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
class EmploymentToValidator implements ImportRowValidatorInterface
{
    public function __construct(private MessageService $messageService)
    {
    }

    public function validate(array $row, array $additionalData = []): ?string
    {
        $employmentFrom = $row[ImportEmployeesFromXLSX::COLUMN_EMPLOYMENT_FROM] ?? null;
        $employmentTo = $row[ImportEmployeesFromXLSX::COLUMN_EMPLOYMENT_TO] ?? null;

        if (!empty($employmentTo)) {
            $errorMessage = DateFormatValidator::validate($employmentTo, DateFormatEnum::YYYY_MM_DD->value);
            if (null !== $errorMessage) {
                return $this->messageService->get('employee.employmentTo.'.$errorMessage, [':dateFormat' => DateFormatEnum::YYYY_MM_DD->value], 'employees');
            }

            if (!empty($employmentFrom)) {
                $fromDate = \DateTime::createFromFormat('Y-m-d', $employmentFrom);
                $toDate = \DateTime::createFromFormat('Y-m-d', $employmentTo);

                if ($fromDate && $toDate && $toDate <= $fromDate) {
                    return $this->messageService->get(
                        'employee.employmentTo.mustBeAfterEmploymentFrom',
                        ['from' => $employmentFrom, 'to' => $employmentTo],
                        'employees'
                    );
                }
            }
        }

        return null;
    }
}
