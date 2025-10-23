<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Industry;

use App\Common\XLSX\XLSXIterator;
use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImportIndustriesFromXLSX extends XLSXIterator
{
    public const COLUMN_NAME = 0;
    public const COLUMN_DESCRIPTION = 1;

    public function __construct(
        private readonly string $filePath,
        private readonly TranslatorInterface $translator,
        private readonly IndustryReaderInterface $industryReaderRepository,
    ) {
        parent::__construct($this->filePath, $this->translator);
    }

    public function validateRow(array $row, int $index): array
    {
        $errorMessages = [];
        [$industryName] = $row + [null];

        if ($errorMessage = $this->validateIndustryName($industryName)) {
            $errorMessages[] = $errorMessage;
        }

        if ($this->industryExists($industryName)) {
            $errorMessages[] = $this->formatErrorMessage('industry.name.alreadyExists', [':name' => $industryName]);
        }

        return $errorMessages;
    }

    private function validateIndustryName(?string $industryName): ?string
    {
        if (empty($industryName)) {
            return $this->formatErrorMessage('industry.name.required');
        }

        if (strlen($industryName) < 3) {
            return $this->formatErrorMessage('industry.name.minimumLength', [':qty' => 3]);
        }

        return null;
    }

    private function industryExists(string $industryName): bool
    {
        return $this->industryReaderRepository->isIndustryNameAlreadyExists($industryName);
    }

    private function formatErrorMessage(string $translationKey, array $parameters = []): string
    {
        return sprintf(
            '%s - %s %d',
            $this->translator->trans($translationKey, $parameters, 'industries'),
            $this->translator->trans('row'),
            count($this->errors) + 2
        );
    }
}
