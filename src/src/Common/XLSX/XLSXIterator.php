<?php

declare(strict_types=1);

namespace App\Common\XLSX;

use App\Common\Domain\Interface\XLSXIteratorInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class XLSXIterator implements XLSXIteratorInterface
{
    protected ?Worksheet $worksheet = null;
    protected array $errors = [];
    protected $rowIndex = 2;

    public function __construct(private readonly string $filePath, private readonly TranslatorInterface $translator)
    {
        ini_set('memory_limit', '2G');
    }

    public function loadFile(): void
    {
        if (!file_exists($this->filePath)) {
            throw new \Exception(sprintf('%s: %s', $this->translator->trans('import.fileNotExists', [], 'validators'), $this->filePath));
        }

        $spreadsheet = IOFactory::load($this->filePath);
        $this->worksheet = $spreadsheet->getActiveSheet();

        if ($this->worksheet->getHighestRow() < 2) {
            throw new \Exception($this->translator->trans('import.noData', [], 'validators'), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
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

            $this->rowIndex++;
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
            $rowData = [];
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            foreach ($cellIterator as $cell) {
                $rowData[] = $cell->getValue();
            }

            $data[] = $rowData;
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
