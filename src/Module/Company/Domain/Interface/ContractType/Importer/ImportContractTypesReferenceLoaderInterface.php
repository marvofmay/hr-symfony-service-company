<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\ContractType\Importer;

interface ImportContractTypesReferenceLoaderInterface
{
    public function preload(array $rows): void;
    public function mapByName(iterable $contractTypes): array;
}
