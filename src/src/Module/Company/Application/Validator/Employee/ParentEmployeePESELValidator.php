<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Employee;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\Service\Employee\ImportEmployeesFromXLSX;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use App\Common\Shared\Utils\PESELValidator as PESEL;

#[AutoconfigureTag('app.import_employees_validator')]
class ParentEmployeePESELValidator implements ImportRowValidatorInterface
{
    public function __construct(private MessageService $messageService) {}

    public function validate(array $row, array $additionalData = []): ?string
    {
        $parentEmployeePESEL = $row[ImportEmployeesFromXLSX::COLUMN_PARENT_EMPLOYEE_PESEL] ?? null;
        if (empty($parentEmployeePESEL)) {
            return null;
        }

        $errorMessage = PESEL::validate($parentEmployeePESEL);
        if (null !== $errorMessage) {
            return $this->messageService->get($errorMessage, [], 'validators');
        }

        return null;
    }
}