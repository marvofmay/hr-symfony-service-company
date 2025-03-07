<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Employee;

class ImportDTO
{
    public function __construct(public ?string $uploadFilePath, public ?string $fileName)
    {
    }
}
