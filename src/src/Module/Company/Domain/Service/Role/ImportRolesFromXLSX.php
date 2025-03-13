<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role;

use App\Common\XLSX\XLSXIterator;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImportRolesFromXLSX extends XLSXIterator
{
    public const COLUMN_NAME = 0;
    public const COLUMN_DESCRIPTION = 1;

    public function __construct(
        private readonly string $filePath,
        private readonly TranslatorInterface $translator,
        private readonly RoleReaderInterface $roleReaderRepository,
    ) {
        parent::__construct($this->filePath, $this->translator);
    }

    public function validateRow(array $row): array
    {
        $errorMessages = [];
        [$roleName] = $row + [null];

        if ($errorMessage = $this->validateRoleName($roleName)) {
            $errorMessages[] = $errorMessage;
        }

        if ($this->roleExists($roleName)) {
            $errorMessages[] = $this->formatErrorMessage('role.name.alreadyExists');
        }

        return $errorMessages;
    }

    private function validateRoleName(?string $roleName): ?string
    {
        if (empty($roleName)) {
            return $this->formatErrorMessage('role.name.required');
        }

        if (strlen($roleName) < 3) {
            return $this->formatErrorMessage('role.name.minimumLength', [':qty' => 3]);
        }

        return null;
    }

    private function roleExists(string $roleName): bool
    {
        return $this->roleReaderRepository->isRoleExists($roleName);
    }

    private function formatErrorMessage(string $translationKey, array $parameters = []): string
    {
        return sprintf(
            '%s - %s %d',
            $this->translator->trans($translationKey, $parameters, 'roles'),
            $this->translator->trans('row'),
            count($this->errors) + 2
        );
    }
}
