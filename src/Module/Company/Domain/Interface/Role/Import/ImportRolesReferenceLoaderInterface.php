<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Role\Import;

interface ImportRolesReferenceLoaderInterface
{
    public function preload(array $rows): void;
    public function mapByName(iterable $roles): array;
}
