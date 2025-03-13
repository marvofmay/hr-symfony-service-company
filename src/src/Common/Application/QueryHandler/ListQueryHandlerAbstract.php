<?php

declare(strict_types=1);

namespace App\Common\Application\QueryHandler;

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

    abstract protected function transformIncludes(array $items, array $includes): array;

    public function handle($query): array
    {
        $queryBuilder = $this->createBaseQueryBuilder();

        $queryBuilder = $this->setFilters($queryBuilder, $query->getFilters());

        $totalItems = count($queryBuilder->getQuery()->getResult());

        $queryBuilder = $queryBuilder
            ->orderBy($this->getAlias().'.'.$query->getOrderBy() ?? $this->getDefaultOrderBy(), $query->getOrderDirection())
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
        return $this->entityManager->createQueryBuilder()
            ->select($this->getAlias())
            ->from($this->getEntityClass(), $this->getAlias());
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
}
