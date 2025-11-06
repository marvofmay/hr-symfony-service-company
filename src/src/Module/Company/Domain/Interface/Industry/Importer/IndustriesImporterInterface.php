<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Industry\Importer;

interface IndustriesImporterInterface
{
    public function save(array $preparedRows, array $existingIndustries): void;
}