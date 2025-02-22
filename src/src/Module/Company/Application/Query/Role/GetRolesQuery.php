<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Query\Role;

use App\Module\Company\Domain\DTO\Role\RolesQueryDTO;
use App\Module\Company\Domain\Entity\Role;

class GetRolesQuery
{
    public function __construct(
        private readonly RolesQueryDTO $queryDTO,
        private int $limit = 10,
        private int $page = 1,
        private int $offset = 0,
        private string $orderBy = 'createdAt',
        private string $orderDirection = 'DESC',
        private array $filters = [],
    ) {
        $this->limit = $this->queryDTO->pageSize;
        $this->page = $this->queryDTO->page;
        $this->orderBy = $this->queryDTO->sortBy;
        $this->orderDirection = $this->queryDTO->sortDirection;
        $this->filters = array_filter((array) $this->queryDTO, fn ($key) => in_array($key, Role::getAttributes()), ARRAY_FILTER_USE_KEY);
        $this->offset = ($this->queryDTO->page - 1) * $this->limit;

        foreach ((array) $this->queryDTO as $key => $val) {
            if ('deleted' === $key && in_array($val, ['0', '1', 'true', 'false'])) {
                $this->filters[$key] = $val;
            }
            if ('phrase' === $key) {
                $this->filters[$key] = $val;
            }
        }
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getOrderBy(): string
    {
        return $this->orderBy;
    }

    public function getOrderDirection(): string
    {
        return $this->orderDirection;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }
}
