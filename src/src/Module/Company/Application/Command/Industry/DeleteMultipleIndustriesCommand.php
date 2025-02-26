<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Industry;

class DeleteMultipleIndustriesCommand
{
    public function __construct(public array $selectedUUID)
    {
    }
}
