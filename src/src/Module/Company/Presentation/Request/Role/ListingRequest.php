<?php

namespace App\module\company\Presentation\Request\Role;

use App\module\company\Domain\Entity\Role;
use Symfony\Component\HttpFoundation\Request;

class ListingRequest
{
    public function __construct(
        public Request $request,
        private array $data = [],
        public ?int $limit = null,
        public ?int $page = null,
        public ?string $orderBy = null,
        public ?string $orderDirection = null,
        public array $filters = []
    ) {
        $this->data = ! empty($request->query->all()) ? $request->query->all() : [];
        $this->limit = $this->data['pageSize'] ?? null;
        $this->page = $this->data['pageIndex'] ?? null;
        $this->orderBy = $this->data['sortBy'] ?? null;
        $this->orderDirection = $this->data['sortDirection'] ?? null;
        $this->filters = array_filter($this->data, fn ($key) => in_array($key, Role::getAttributes()), ARRAY_FILTER_USE_KEY);

        foreach ($this->data as $key => $val) {
            if ($key === 'deleted' && in_array($val, ["0", "1", "true", "false"])) {
                $this->filters[$key] = $val;
            }
            if ($key === 'phrase') {
                $this->filters[$key] = $val;
            }
        }
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function getOrderBy(): ?string
    {
        return $this->orderBy;
    }

    public function getOrderDirection(): ?string
    {
        return $this->orderDirection;
    }

    public function getFilters (): array
    {
        return $this->filters;
    }
}