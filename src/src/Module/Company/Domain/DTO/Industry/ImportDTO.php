<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Industry;

class ImportDTO
{
    public function __construct(public ?string $importUUID)
    {
    }
}
