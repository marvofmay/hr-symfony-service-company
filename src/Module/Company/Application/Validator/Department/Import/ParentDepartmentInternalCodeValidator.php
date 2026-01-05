<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Department\Import;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\Enum\DepartmentImportColumnEnum;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.department.import.validator')]
final readonly class ParentDepartmentInternalCodeValidator implements ImportRowValidatorInterface
{
    public function __construct(
        private DepartmentReaderInterface $departmentReaderRepository,
        private MessageService $messageService
    ) {
    }

    public function validate(array $row, array $additionalData = []): ?string
    {
        $parentDepartmentInternalCode = (string)$row[DepartmentImportColumnEnum::PARENT_DEPARTMENT_INTERNAL_CODE->value] ?? null;
        if (empty($parentDepartmentInternalCode)) {
            return null;
        }
        $isDepartmentExists = $this->departmentReaderRepository->isDepartmentExistsWithInternalCode($parentDepartmentInternalCode);
        if (!$isDepartmentExists) {
            return $this->messageService->get('department.internalCode.notExists', [':internalCode' => $parentDepartmentInternalCode], 'departments');
        }

        return null;
    }
}
