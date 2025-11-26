<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Industry\Importer;

interface ImportIndustriesReferenceLoaderInterface
{
    public function preload(array $rows): void;
    public function mapByName(iterable $industries): array;
}