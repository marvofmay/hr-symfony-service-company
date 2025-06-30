<?php

declare(strict_types=1);

namespace App\Common\Domain\Interface;

use App\Common\Application\Query\ListQueryAbstract;
use Doctrine\ORM\QueryBuilder;

interface ListQueryHandlerInterface
{
    public function getEntityClass(): string;
    public function getAlias(): string;
    public function getDefaultOrderBy(): string;
    public function getAllowedFilters(): array;
    public function getRelations(): array;
    public function handle(ListQueryAbstract $query): array;
    public function createBaseQueryBuilder(): QueryBuilder;
    public function getTransformer(): object;
    public function transformIncludes(array $items, array $includes): array;
    public function transformItem($transformer, $item, array $includes): array;
}