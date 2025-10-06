<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Shared\Import;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\Service\Employee\ImportEmployeesFromXLSX;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.import_shared_validator')]
class CityValidator implements ImportRowValidatorInterface
{
    public function __construct(private MessageService $messageService) {}

    public function validate(array $row, array $additionalData = []): ?string
    {
        $city = $row[ImportEmployeesFromXLSX::COLUMN_CITY] ?? null;
        if (null === $city) {
            return $this->messageService->get('employee.city.required', [], 'employees');
        }

        return null;
    }
}