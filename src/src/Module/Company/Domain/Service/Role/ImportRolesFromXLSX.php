<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role;

use App\Common\XLSX\XLSXIterator;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImportRolesFromXLSX extends XLSXIterator
{
    public function __construct(
        private readonly string $filePath,
        private readonly TranslatorInterface $translator,
        private readonly RoleReaderInterface $roleReaderRepository
    ) {
        parent::__construct($this->filePath, $this->translator);
    }

    public function validateRow(array $row): ?string
    {
        [$roleName] = $row + [null];

        if ($errorMessage = $this->validateRoleName($roleName)) {
            return $errorMessage;
        }

        if ($this->roleExists($roleName)) {
            return $this->formatErrorMessage('role.name.alreadyExists');
        }

        return null;
    }

    private function validateRoleName(?string $roleName): ?string
    {
        if (empty($roleName)) {
            return $this->formatErrorMessage('role.name.nameIsRequired');
        }

        if (strlen($roleName) < 3) {
            return $this->formatErrorMessage('role.name.minimumLetters', [':qty' => 3]);
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
            (count($this->errors) + 2)
        );
    }
}