<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\ContractType\Importer;

interface ContractTypesImporterInterface
{
    public function save(array $preparedRows, array $existingContractTypes): void;
}