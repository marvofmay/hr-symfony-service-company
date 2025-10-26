<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Query\Position;

use App\Common\Domain\Interface\QueryInterface;

final readonly class GetPositionByUUIDQuery implements QueryInterface
{
    public const string POSITION_UUID = 'positionUUID';

    public function __construct(public string $positionUUID)
    {
    }
}
