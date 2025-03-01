<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\ContractType;

class ImportDTO
{
    private array $data = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
