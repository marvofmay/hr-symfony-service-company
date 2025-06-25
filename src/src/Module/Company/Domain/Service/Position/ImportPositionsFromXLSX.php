<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Position;

use App\Common\XLSX\XLSXIterator;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImportPositionsFromXLSX extends XLSXIterator
{
    public const COLUMN_NAME = 0;
    public const COLUMN_DESCRIPTION = 1;
    public const COLUMN_ACTIVE = 2;
    public const COLUMN_DEPARTMENT_UUID = 3;

    public function __construct(
        private readonly string $filePath,
        private readonly TranslatorInterface $translator,
        private readonly PositionReaderInterface $positionReaderRepository,
        private readonly DepartmentReaderInterface $departmentReaderRepository,
    ) {
        parent::__construct($this->filePath, $this->translator);
    }

    public function validateRow(array $row): array
    {
        $errorMessages = [];
        [$positionName, $positionDescription, $positionActive, $departmentUUID] = $row + [null, null, null, null];

        if ($errorMessage = $this->validatePositionName($positionName)) {
            $errorMessages[] = $errorMessage;
        }

        if ($this->positionExists($positionName)) {
            $errorMessages[] = $this->formatErrorMessage('position.name.alreadyExists');
        }

        if (!$this->isDepartmentWithUUIDExists($departmentUUID)) {
            $errorMessages[] = $this->formatErrorMessage('department.uuid.notExists', [':uuid' => $departmentUUID], 'departments');
        }

        return $errorMessages;
    }

    private function validatePositionName(?string $positionName): ?string
    {
        if (empty($positionName)) {
            return $this->formatErrorMessage('position.name.required');
        }

        if (strlen($positionName) < 3) {
            return $this->formatErrorMessage('position.name.minimumLength', [':qty' => 3]);
        }

        return null;
    }

    private function positionExists(string $positionName): bool
    {
        return $this->positionReaderRepository->isPositionExists($positionName);
    }

    private function isDepartmentWithUUIDExists(string $departmentUUID): bool
    {
        return $this->departmentReaderRepository->isDepartmentExistsWithUUID($departmentUUID);
    }

    private function formatErrorMessage(string $translationKey, array $parameters = [], string $domain = 'positions'): string
    {
        return sprintf(
            '%s - %s %d',
            $this->translator->trans($translationKey, $parameters, $domain),
            $this->translator->trans('row'),
            count($this->errors) + 2
        );
    }

    public function groupPositions(): array
    {
        $groupedPositions = [];

        foreach ($this->worksheet->getRowIterator(2) as $row) {
            $cells = $row->getCellIterator();
            $cells->setIterateOnlyExistingCells(true);

            $data = [];
            foreach ($cells as $cell) {
                $data[] = $cell->getValue();
            }

            [$name, $description, $active, $departmentUUID] = $data;

            $key = md5($name.$description.$active);
            if (!isset($groupedPositions[$key])) {
                $groupedPositions[$key] = [
                    self::COLUMN_NAME => $name,
                    self::COLUMN_DESCRIPTION => $description,
                    self::COLUMN_ACTIVE => $active,
                    self::COLUMN_DEPARTMENT_UUID => [],
                ];
            }

            if (!in_array($departmentUUID, $groupedPositions[$key][self::COLUMN_DEPARTMENT_UUID])) {
                $groupedPositions[$key][self::COLUMN_DEPARTMENT_UUID][] = $departmentUUID;
            }
        }

        return array_values($groupedPositions);
    }
}
