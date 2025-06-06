<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Department;

class ImportDTO
{
    public function __construct(public ?string $importUUID)
    {
    }
}
