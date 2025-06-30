<?php

declare(strict_types=1);

namespace App\Common\Domain\Interface;

interface ListQueryInterface
{
    public function getAttributes(): array;

    public function getRelations(): array;

    public function getLimit(): int;
    public function getPage(): int;
    public function getOffset(): int;
    public function getOrderBy(): string;
    public function getOrderDirection(): string;
    public function getFilters(): array;
    public function getIncludes(): array;
}