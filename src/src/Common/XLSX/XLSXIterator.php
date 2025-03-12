<?php

declare(strict_types=1);

namespace App\Common\XLSX;

use App\Common\Domain\Interface\XLSXIteratorInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class XLSXIterator implements XLSXIteratorInterface
{
    protected ?Worksheet $worksheet = null;
    protected array $errors = [];

    public function __construct(private readonly string $filePath, private readonly TranslatorInterface $translator)
    {
    }

    public function loadFile(): void
    {
        if (!file_exists($this->filePath)) {
            throw new \RuntimeException(sprintf('%s: %s', $this->translator->trans('import.fileNotExists', [], 'validators'), $this->filePath));
        }

        $spreadsheet = IOFactory::load($this->filePath);
        $this->worksheet = $spreadsheet->getActiveSheet();
    }

    public function validateBeforeImport(): array
    {
        $this->loadFile();

        if (!$this->worksheet) {
            throw new \RuntimeException($this->translator->trans('import.chooseFile', [], 'validators'));
        }

        foreach ($this->worksheet->getRowIterator(2) as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $rowData = [];
            foreach ($cellIterator as $cell) {
                $rowData[] = $cell->getValue();
            }

            if ($error = $this->validateRow($rowData)) {
                $this->errors = array_merge($this->errors, $error);
            }
        }

        return $this->errors;
    }

    public function iterateRows(): array
    {
        if (!$this->worksheet) {
            throw new \RuntimeException($this->translator->trans('import.chooseFile', [], 'validators'));
        }

        $data = [];
        foreach ($this->worksheet->getRowIterator(2) as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            foreach ($cellIterator as $cell) {
                $data[] = $cell->getValue();
            }
        }

        return $data;
    }

    public function import(): array
    {
        $this->loadFile();

        return $this->iterateRows();
    }

    abstract public function validateRow(array $row): array;

    public function getErrors(): array
    {
        return $this->errors;
    }
}
