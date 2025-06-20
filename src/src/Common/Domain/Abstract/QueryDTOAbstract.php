<?php

declare(strict_types=1);

namespace App\Common\Domain\Abstract;

use App\Common\Domain\Interface\QueryDTOInterface;

class QueryDTOAbstract implements QueryDTOInterface
{
    public ?string $createdAt = null;

    public ?string $updatedAt = null;

    public ?string $deletedAt = null;

    public ?int $page = 1;

    public ?int $pageSize = 10;

    public ?string $sortBy = 'createdAt';

    public ?string $sortDirection = 'desc';

    public ?int $deleted = null;

    public ?string $phrase = null;

    public ?string $includes = null;
}
