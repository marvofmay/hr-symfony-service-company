<?php

declare(strict_types=1);

namespace App\Module\Company\Application\DTO\Industry;

use App\Common\Domain\Abstract\QueryDTOAbstract;

class IndustriesQueryDTO extends QueryDTOAbstract
{
    public ?string $name = null;

    public ?string $description = null;
}
