<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Employee;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\Service\Employee\ImportEmployeesFromXLSX;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.import_employees_validator')]
class PositionUUIDValidator implements ImportRowValidatorInterface
{
    public function __construct(private MessageService $messageService) {}

    public function validate(array $row, array $additionalData = []): ?string
    {
        $positionUUID = $row[ImportEmployeesFromXLSX::COLUMN_POSITION_UUID] ?? null;
        if (empty($positionUUID)) {
            return $this->messageService->get('employee.positionUUID.required', [], 'employees');
        }

        $positionExists = isset($additionalData['positions'][$positionUUID]);
        if (!$positionExists) {
            return $this->messageService->get('position.uuid.notExists', [':uuid' => $positionUUID], 'positions');
        }

        return null;
    }
}