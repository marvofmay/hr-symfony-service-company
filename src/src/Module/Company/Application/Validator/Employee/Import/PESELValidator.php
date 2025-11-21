<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Employee\Import;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Common\Shared\Utils\PESELValidator as PESEL;
use App\Module\Company\Domain\Enum\EmployeeImportColumnEnum;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.employee.import.validator')]
final readonly class PESELValidator implements ImportRowValidatorInterface
{
    public function __construct(private MessageService $messageService)
    {
    }

    public function validate(array $row, array $additionalData = []): ?string
    {
        $pesel = (string) $row[EmployeeImportColumnEnum::PESEL->value] ?? null;
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
