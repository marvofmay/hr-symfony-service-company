<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Position\Import;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\Enum\Position\PositionImportColumnEnum;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.position.import.validator')]
final readonly class DepartmentWithInternalCodeExistsValidator implements ImportRowValidatorInterface
{
    public function __construct(
        private DepartmentReaderInterface $departmentReaderRepository,
        private MessageService $messageService
    ) {
    }

    public function validate(array $row, array $additionalData = []): ?string
    {
        $departmentInternalCode = (string)$row[PositionImportColumnEnum::DEPARTMENT_INTERNAL_CODE->value] ?? null;
        if (empty($departmentInternalCode)) {
            return null;
        }

        $isDepartmentExists = $this->departmentReaderRepository->isDepartmentExistsWithInternalCode($departmentInternalCode);
        if (!$isDepartmentExists) {
            return $this->messageService->get('department.internalCode.notExists', [':internalCode' => $departmentInternalCode], 'departments');
        }

        return null;
    }
}
