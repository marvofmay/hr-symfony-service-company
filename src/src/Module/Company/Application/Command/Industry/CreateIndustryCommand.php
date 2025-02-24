<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Industry;

class CreateIndustryCommand
{
    public function __construct(public string $name, public ?string $description)
    {
    }
}
