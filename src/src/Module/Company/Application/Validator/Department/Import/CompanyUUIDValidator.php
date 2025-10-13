<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Department\Import;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\Service\Department\ImportDepartmentsFromXLSX;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.import_department_validator')]
class CompanyUUIDValidator implements ImportRowValidatorInterface
{
    public function __construct(private MessageService $messageService)
    {
    }

    public function validate(array $row, array $additionalData = []): ?string
    {
        $companyUUID = $row[ImportDepartmentsFromXLSX::COLUMN_COMPANY_UUID] ?? null;
        if (empty($companyUUID)) {
            return $this->messageService->get('department.companyUUID.required', [], 'departments');
        }

        $companyExists = isset($additionalData['companies'][$companyUUID]);
        if (!$companyExists) {
            return $this->messageService->get('company.uuid.notExists', [':uuid' => $companyUUID], 'companies');
        }

        return null;
    }
}
