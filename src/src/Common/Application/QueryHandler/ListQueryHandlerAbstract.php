<?php

declare(strict_types=1);

namespace App\Common\Application\QueryHandler;

use App\Common\Application\Factory\TransformerFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

abstract class ListQueryHandlerAbstract
{
    public function __construct(protected readonly EntityManagerInterface $entityManager)
    {
    }

    abstract protected function getEntityClass(): string;

    abstract protected function getAlias(): string;

    abstract protected function getDefaultOrderBy(): string;

    abstract protected function getAllowedFilters(): array;

    abstract protected function getRelations(): array;

    public function handle($query): array
    {
        $queryBuilder = $this->createBaseQueryBuilder();
        $queryBuilder = $this->setFilters($queryBuilder, $query->getFilters());
        $totalItems = (clone $queryBuilder)->select("COUNT({$this->getAlias()}.uuid)")->getQuery()->getSingleScalarResult();
        foreach ($query->getIncludes() as $relation) {
            if (in_array($relation, $this->getRelations(), true)) {
                $queryBuilder->leftJoin("{$this->getAlias()}.$relation", $relation)
                    ->addSelect($relation);
            }
        }

        $orderByField = $query->getOrderBy() ?? $this->getDefaultOrderBy();
        if (strpos($orderByField, '.') === false) {
            $orderByField = "{$this->getAlias()}.$orderByField";
        }

        $queryBuilder->orderBy($orderByField, $query->getOrderDirection());
        $queryBuilder
            ->setMaxResults($query->getLimit())
            ->setFirstResult($query->getOffset());

        $items = $queryBuilder->getQuery()->getResult();

        return [
            'total' => $totalItems,
            'page' => $query->getPage(),
            'limit' => $query->getLimit(),
            'items' => $this->transformIncludes($items, $query->getIncludes()),
        ];
    }

    private function createBaseQueryBuilder(): QueryBuilder
    {
        return $this->entityManager->getRepository($this->getEntityClass())->createQueryBuilder($this->getAlias());
    }

    abstract protected function getPhraseSearchColumns(): array;

    private function setFilters(QueryBuilder $queryBuilder, array $filters): QueryBuilder
    {
        $alias = $this->getAlias();
        $allowedFilters = $this->getAllowedFilters();

        foreach ($filters as $fieldName => $fieldValue) {
            if (is_null($fieldValue) || !in_array($fieldName, $allowedFilters, true)) {
                continue;
            }
            $queryBuilder->andWhere($queryBuilder->expr()->like("$alias.$fieldName", ':fieldValue'))
                ->setParameter('fieldValue', "%$fieldValue%");
        }

        if (array_key_exists('deleted', $filters)) {
            $deletedColumn = "$alias.deletedAt";
            switch ($filters['deleted']) {
                case 0:
                    $queryBuilder->andWhere($queryBuilder->expr()->isNull($deletedColumn));
                    break;
                case 1:
                    $this->entityManager->getFilters()->disable('soft_delete');
                    $queryBuilder->andWhere($queryBuilder->expr()->isNotNull($deletedColumn));
                    break;
            }
        } else {
            $queryBuilder->andWhere($queryBuilder->expr()->isNull("$alias.deletedAt"));
        }

        if (!empty($filters['phrase'])) {
            $phraseColumns = $this->getPhraseSearchColumns();
            $expr = $queryBuilder->expr();
            $orConditions = [];

            foreach ($phraseColumns as $column) {
                $orConditions[] = $expr->like("LOWER($alias.$column)", ':searchPhrase');
            }

            if (!empty($orConditions)) {
                $queryBuilder->andWhere(call_user_func_array([$expr, 'orX'], $orConditions))
                    ->setParameter('searchPhrase', '%'.strtolower($filters['phrase']).'%');
            }
        }

        return $queryBuilder;
    }

    protected function getTransformer(): object
    {
        return TransformerFactory::createForHandler(static::class);
    }

    protected function transformIncludes(array $items, array $includes): array
    {
        $transformer = $this->getTransformer();

        return array_map(fn($item) => $this->transformItem($transformer, $item, $includes), $items);
    }

    private function transformItem($transformer, $item, array $includes): array
    {
        return method_exists($transformer, 'transformToArray')
            ? $transformer->transformToArray($item, $includes)
            : throw new \RuntimeException('Transformer does not implement transformToArray method.');
    }
}
