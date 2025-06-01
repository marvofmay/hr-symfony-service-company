<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\ContractType;

use App\Common\XLSX\XLSXIterator;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeReaderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImportContractTypesFromXLSX extends XLSXIterator
{
    public const COLUMN_NAME = 0;
    public const COLUMN_DESCRIPTION = 1;
    public const COLUMN_ACTIVE = 2;

    public function __construct(
        private readonly string $filePath,
        private readonly TranslatorInterface $translator,
        private readonly ContractTypeReaderInterface $contractTypeReaderRepository,
    ) {
        parent::__construct($this->filePath, $this->translator);
    }

    public function validateRow(array $row): array
    {
        $errorMessages = [];
        [$contractTypeName] = $row + [null];

        if ($errorMessage = $this->validateContractTypeName($contractTypeName)) {
            $errorMessages[] = $errorMessage;
        }

        if ($this->contractTypeExists($contractTypeName)) {
            $errorMessages[] = $this->formatErrorMessage('contractType.name.alreadyExists');
        }

        return $errorMessages;
    }

    private function validateContractTypeName(?string $contractTypeName): ?string
    {
        if (empty($contractTypeName)) {
            return $this->formatErrorMessage('contractType.name.required');
        }

        if (strlen($contractTypeName) < 3) {
            return $this->formatErrorMessage('contractType.name.minimumLength', [':qty' => 3]);
        }

        return null;
    }

    private function contractTypeExists(string $contractTypeName): bool
    {
        return $this->contractTypeReaderRepository->isContractTypeExists($contractTypeName);
    }

    private function formatErrorMessage(string $translationKey, array $parameters = []): string
    {
        return sprintf(
            '%s - %s %d',
            $this->translator->trans($translationKey, $parameters, 'contract_types'),
            $this->translator->trans('row'),
            count($this->errors) + 2
        );
    }
}
