<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Shared;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\Service\Employee\ImportEmployeesFromXLSX;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.import_shared_validator')]
class CountryValidator implements ImportRowValidatorInterface
{
    public function __construct(private MessageService $messageService) {}

    public function validate(array $row, array $additionalData = []): ?string
    {
        $country = $row[ImportEmployeesFromXLSX::COLUMN_COUNTRY] ?? null;
        if (null === $country) {
            return $this->messageService->get('employee.country.required', [], 'employees');
        }

        return null;
    }
}