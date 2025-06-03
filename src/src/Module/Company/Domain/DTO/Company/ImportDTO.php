<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Company;

class ImportDTO
{
    public function __construct(public ?string $importUUID)
    {
    }
}
