<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service;

use App\Module\Company\Domain\Interface\XLSXIteratorInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use RuntimeException;

abstract class XLSXIterator implements XLSXIteratorInterface
{
    protected ?Worksheet $worksheet = null;
    protected array $errors = [];

    public function __construct(private readonly string $filePath) {}

    public function loadFile(): void
    {
        if (!file_exists($this->filePath)) {
            throw new RuntimeException("Plik nie istnieje: $this->filePath");
        }

        $spreadsheet = IOFactory::load($this->filePath);
        $this->worksheet = $spreadsheet->getActiveSheet();
    }

    public function iterateRows(): array
    {
        if (!$this->worksheet) {
            throw new RuntimeException("Najpierw załaduj plik XLSX.");
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
