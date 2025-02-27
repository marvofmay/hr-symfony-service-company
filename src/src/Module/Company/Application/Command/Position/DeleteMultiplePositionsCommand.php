<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Position;

class DeleteMultiplePositionsCommand
{
    public function __construct(public array $selectedUUID)
    {
    }
}
