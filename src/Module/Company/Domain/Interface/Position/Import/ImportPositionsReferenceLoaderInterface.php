<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Position\Import;

interface ImportPositionsReferenceLoaderInterface
{
    public function preload(array $rows): void;

    public function mapByInternalCode(iterable $departments): array;
    public function mapByName(iterable $positions): array;
}