<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\ContractType\Import;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\Enum\ContractType\ContractTypeImportColumnEnum;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.contract_type.import.validator')]
class NameValidator implements ImportRowValidatorInterface
{
    public const int MINIMUM_LENGTH = 3;
    public const int MAXIMUM_LENGTH = 100;

    public function __construct(private readonly MessageService $messageService)
    {
    }

    public function validate(array $row, array $additionalData = []): ?string
    {
        $name = $row[ContractTypeImportColumnEnum::CONTRACT_TYPE_NAME->value] ?? null;

        if (empty($name)) {
            return $this->messageService->get('contractType.name.required', [], 'contract_types');
        }

        if (strlen($name) < self::MINIMUM_LENGTH) {
            return $this->messageService->get('contractType.name.minimumLength', [':qty' => self::MINIMUM_LENGTH], 'contract_types');
        }
        if (strlen($name) > self::MAXIMUM_LENGTH) {
            return $this->messageService->get('contractType.name.maximumLength', [':qty' => self::MAXIMUM_LENGTH], 'contract_types');
        }

        return null;
    }
}
