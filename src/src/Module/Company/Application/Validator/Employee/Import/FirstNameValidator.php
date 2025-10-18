<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Employee\Import;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\Enum\EmployeeImportColumnEnum;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.import_employee_validator')]
class FirstNameValidator implements ImportRowValidatorInterface
{
    public const int MINIMUM_LENGTH = 3;

    public function __construct(private MessageService $messageService)
    {
    }

    public function validate(array $row, array $additionalData = []): ?string
    {
        $firstName = $row[EmployeeImportColumnEnum::FIRST_NAME->value] ?? null;

        if (empty($firstName)) {
            return $this->messageService->get('employee.firstName.required', [], 'employees');
        }

        if (strlen($firstName) < self::MINIMUM_LENGTH) {
            return $this->messageService->get('employee.firstName.minimumLength', [':qty' => self::MINIMUM_LENGTH], 'employees');
        }

        return null;
    }
}
