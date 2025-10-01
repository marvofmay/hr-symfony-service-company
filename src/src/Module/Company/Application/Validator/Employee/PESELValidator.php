<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Employee;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\Service\Employee\ImportEmployeesFromXLSX;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use App\Common\Shared\Utils\PESELValidator as PESEL;

#[AutoconfigureTag('app.import_employees_validator')]
class PESELValidator implements ImportRowValidatorInterface
{
    public function __construct(private MessageService $messageService) {}

    public function validate(array $row, array $additionalData = []): ?string
    {
        $pesel = $row[ImportEmployeesFromXLSX::COLUMN_PESEL] ?? null;
        if (null === $pesel) {
            return $this->messageService->get('employee.pesel.required', [], 'employees');
        }

        $errorMessage = PESEL::validate($pesel);
        if (null !== $errorMessage) {
            return $this->messageService->get($errorMessage, [], 'validators');
        }

        return null;
    }
}