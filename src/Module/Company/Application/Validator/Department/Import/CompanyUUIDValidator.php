<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Department\Import;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\Enum\DepartmentImportColumnEnum;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.department.import.validator')]
final readonly class CompanyUUIDValidator implements ImportRowValidatorInterface
{
    public function __construct(private MessageService $messageService)
    {
    }

    public function validate(array $row, array $additionalData = []): ?string
    {
        $companyUUID = $row[DepartmentImportColumnEnum::COMPANY_UUID->value] ?? null;
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
