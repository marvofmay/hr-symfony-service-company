<?php

declare(strict_types=1);

namespace App\Common\Application\Query;

use App\Common\Domain\Interface\QueryDTOInterface;

abstract class ListQueryAbstract
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
            fn ($key) => in_array($key, $this->getAttributes()),
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

    abstract protected function getAttributes(): array;
    abstract protected function getRelations(): array;

    public function getLimit(): int { return $this->limit; }
    public function getPage(): int { return $this->page; }
    public function getOffset(): int { return $this->offset; }
    public function getOrderBy(): string { return $this->orderBy; }
    public function getOrderDirection(): string { return $this->orderDirection; }
    public function getFilters(): array { return $this->filters; }
    public function getIncludes(): array { return $this->includes; }
}
