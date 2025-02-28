<?php

declare(strict_types=1);

namespace App\Common\XLSX;

use App\Common\Interface\XLSXIteratorInterface;
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

    public function iterateRows(): array
    {
        if (!$this->worksheet) {
            throw new \RuntimeException($this->translator->trans('import.chooseFile', [], 'validators'));
        }

        $data = [];
        foreach ($this->worksheet->getRowIterator(2) as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $rowData = [];
            foreach ($cellIterator as $cell) {
                $rowData[] = $cell->getValue();
            }

            if ($error = $this->validateRow($rowData)) {
                $this->errors[] = $error;
            } else {
                $data[] = $rowData;
            }
        }

        return $data;
    }

    public function import(): array
    {
        $this->loadFile();

        return $this->iterateRows();
    }

    abstract public function validateRow(array $row): ?string;

    public function getErrors(): array
    {
        return $this->errors;
    }
}
