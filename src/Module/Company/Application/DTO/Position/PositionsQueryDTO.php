<?php

declare(strict_types=1);

namespace App\Module\Company\Application\DTO\Position;

use App\Common\Domain\Abstract\QueryDTOAbstract;

class PositionsQueryDTO extends QueryDTOAbstract
{
    public ?string $name = null;

    public ?string $description = null;

    public ?bool $active = null;
}
