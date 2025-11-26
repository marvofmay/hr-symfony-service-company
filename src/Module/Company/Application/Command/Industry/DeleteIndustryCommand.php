<?php

namespace App\Module\Company\Application\Command\Industry;

use App\Common\Domain\Interface\CommandInterface;

readonly class DeleteIndustryCommand implements CommandInterface
{
    public const string INDUSTRY_UUID = 'industryUUID';

    public function __construct(public string $industryUUID)
    {
    }
}
