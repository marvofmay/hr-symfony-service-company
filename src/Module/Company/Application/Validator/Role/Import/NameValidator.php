<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Role\Import;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\Enum\Role\RoleImportColumnEnum;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.role.import.validator')]
final readonly class NameValidator implements ImportRowValidatorInterface
{
    public const int MINIMUM_LENGTH = 3;
    public const int MAXIMUM_LENGTH = 100;

    public function __construct(private MessageService $messageService)
    {
    }

    public function validate(array $row, array $additionalData = []): ?string
    {
        $name = $row[RoleImportColumnEnum::ROLE_NAME->value] ?? null;

        if (empty($name)) {
            return $this->messageService->get('role.name.required', [], 'roles');
        }

        if (strlen($name) < self::MINIMUM_LENGTH) {
            return $this->messageService->get('role.name.minimumLength', [':qty' => self::MINIMUM_LENGTH], 'roles');
        }
        if (strlen($name) > self::MAXIMUM_LENGTH) {
            return $this->messageService->get('role.name.maximumLength', [':qty' => self::MAXIMUM_LENGTH], 'roles');
        }

        return null;
    }
}
