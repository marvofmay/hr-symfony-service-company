<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Query\Position;

final readonly class GetPositionByUUIDQuery
{
    public function __construct(public string $uuid)
    {
    }
}
