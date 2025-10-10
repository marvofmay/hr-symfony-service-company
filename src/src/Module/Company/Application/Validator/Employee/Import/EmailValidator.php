<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Employee\Import;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Common\Shared\Utils\EmailValidator as Email;
use App\Module\Company\Domain\Service\Employee\ImportEmployeesFromXLSX;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.import_employee_validator')]
class EmailValidator implements ImportRowValidatorInterface
{
    public function __construct(private MessageService $messageService)
    {
    }

    public function validate(array $row, array $additionalData = []): ?string
    {
        $email = $row[ImportEmployeesFromXLSX::COLUMN_EMAIL] ?? null;
        if (null === $email) {
            return $this->messageService->get('employee.email.required', [], 'employees');
        }

        $errorMessage = Email::validate($email);
        if (null !== $errorMessage) {
            return $this->messageService->get($errorMessage, [], 'validators');
        }

        // ToDo:: check if employee with an email and a different PESEL alreday exists in the DB

        return null;
    }
}
