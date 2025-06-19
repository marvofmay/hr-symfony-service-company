<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Query\Industry;

final readonly class GetIndustryByUUIDQuery
{
    public function __construct(public string $uuid)
    {
    }
}