<?php

declare(strict_types=1);

namespace App\Common\Application\Query;

use App\Common\Domain\Interface\ListQueryInterface;
use App\Common\Domain\Interface\QueryDTOInterface;

abstract class ListQueryAbstract implements ListQueryInterface
{
    protected int $limit;
    protected int $page;
    protected int $offset;
    protected string $orderBy;
    protected string $orderDirection;
    protected array $filters = [];
    protected array $includes = [];

    public function __construct(protected QueryDTOInterface $queryDTO)
    {
        $this->limit = $this->queryDTO->pageSize;
        $this->page = $this->queryDTO->page;
        $this->orderBy = $this->queryDTO->sortBy;
        $this->orderDirection = $this->queryDTO->sortDirection;
        $this->offset = ($this->page - 1) * $this->limit;

        $this->filters = array_filter(
            (array) $this->queryDTO,
            function (string $key): bool {
                if (in_array($key, $this->getAttributes(), true)) {
                    return true;
                }

                if (str_ends_with($key, 'UUID')) {
                    $relation = lcfirst(substr($key, 0, -4));
                    return in_array($relation, $this->getRelations(), true);
                }

                return false;
            },
            ARRAY_FILTER_USE_KEY
        );

        $this->includes = array_filter(
            explode(',', $this->queryDTO->includes ?? ''),
            fn ($relation) => in_array($relation, $this->getRelations())
        );

        foreach ((array) $this->queryDTO as $key => $val) {
            if ('deleted' === $key && in_array($val, ['0', '1', 'true', 'false'])) {
                $this->filters[$key] = $val;
            }
            if ('phrase' === $key) {
                $this->filters[$key] = $val;
            }
        }
    }

    abstract public function getAttributes(): array;

    abstract public function getRelations(): array;

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

    public function getIncludes(): array
    {
        return $this->includes;
    }

    public function getQueryDTO(): QueryDTOInterface
    {
        return $this->queryDTO;
    }
}
