<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Industry;

use App\Common\Domain\Interface\CommandInterface;

final readonly class UpdateIndustryCommand implements CommandInterface
{
    public const string INDUSTRY_UUID = 'industryUUID';
    public const string INDUSTRY_NAME = 'name';
    public const string INDUSTRY_DESCRIPTION = 'description';

    public function __construct(
        public string $industryUUID,
        public string $name,
        public ?string $description,
    ) {
    }
}
