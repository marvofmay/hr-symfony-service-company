<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Industry;

use App\Common\Domain\Interface\CommandInterface;

final readonly class RestoreIndustryCommand implements CommandInterface
{
    public const string INDUSTRY_UUID = 'industryUUID';

    public function __construct(public string $industryUUID)
    {
    }
}
