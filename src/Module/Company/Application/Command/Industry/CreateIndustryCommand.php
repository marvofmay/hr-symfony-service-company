<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Industry;

use App\Common\Domain\Interface\CommandInterface;

final readonly class CreateIndustryCommand implements CommandInterface
{
    public const string INDUSTRY_NAME = 'name';
    public const string INDUSTRY_DESCRIPTION = 'description';

    public function __construct(public string $name, public ?string $description)
    {
    }
}
