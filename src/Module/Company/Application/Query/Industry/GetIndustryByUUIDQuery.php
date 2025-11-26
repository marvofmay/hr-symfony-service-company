<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Query\Industry;

use App\Common\Domain\Interface\QueryInterface;

final readonly class GetIndustryByUUIDQuery implements QueryInterface
{
    public const string INDUSTRY_UUID = 'industryUUID';

    public function __construct(public string $industryUUID)
    {
    }
}
