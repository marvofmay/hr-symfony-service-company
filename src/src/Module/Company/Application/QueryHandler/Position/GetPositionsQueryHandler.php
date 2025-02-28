<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Position;

use App\Module\Company\Application\Query\Position\GetPositionsQuery;
use App\Module\Company\Domain\Entity\Position;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class GetPositionsQueryHandler
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    public function handle(GetPositionsQuery $query): array
    {
        $limit = $query->getLimit();
        $orderBy = $query->getOrderBy();
        $orderDirection = $query->getOrderDirection();
        $offset = $query->getOffset();
        $filters = $query->getFilters();
        $includes = $query->getIncludes();

        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(Position::class, 'p');

        $queryBuilder = $this->setFilters($queryBuilder, $filters);

        $totalPositions = count($queryBuilder->getQuery()->getResult());

        $queryBuilder = $queryBuilder->orderBy('p.' . $orderBy, $orderDirection)
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        $positions = $queryBuilder->getQuery()->getResult();

        return [
            'totalPositions' => $totalPositions,
            'page' => $query->getPage(),
            'limit' => $query->getLimit(),
            'positions' => $this->transformIncludes($positions, $includes),
        ];
    }

    private function setFilters(QueryBuilder $queryBuilder, array $filters): QueryBuilder
    {
        if (!empty($filters)) {
            foreach ($filters as $fieldName => $fieldValue) {
                if (is_null($fieldValue) || in_array($fieldName, ['deleted', 'phrase'])) {
                    continue;
                }
                if ($fieldName === 'active') {
                    $queryBuilder = $queryBuilder->andWhere('p.' . $fieldName . ' = :fieldValue')
                        ->setParameter('fieldValue', $fieldValue);
                    continue;
                }

                $queryBuilder = $queryBuilder->andWhere($queryBuilder->expr()->like('p.'.$fieldName, ':fieldValue'))
                    ->setParameter('fieldValue', '%'.$fieldValue.'%');
            }

            if (array_key_exists('deleted', $filters)) {
                switch ($filters['deleted']) {
                    case 0:
                        $queryBuilder = $queryBuilder->andWhere($queryBuilder->expr()->isNull('p.'.Position::COLUMN_DELETED_AT));
                        break;
                    case 1:
                        $this->entityManager->getFilters()->disable('soft_delete');
                        $queryBuilder = $queryBuilder->andWhere($queryBuilder->expr()->isNotNull('p.'.Position::COLUMN_DELETED_AT));
                        break;
                }
            } else {
                $queryBuilder = $queryBuilder->andWhere($queryBuilder->expr()->isNull('p.'.Position::COLUMN_DELETED_AT));
            }

            if (array_key_exists('phrase', $filters) && !empty($filters['phrase'])) {
                $queryBuilder = $queryBuilder->andWhere(
                    $queryBuilder->expr()->orX(
                        $queryBuilder->expr()->like('LOWER(p.'.Position::COLUMN_NAME.')', ':searchPhrase'),
                        $queryBuilder->expr()->like('LOWER(p.'.Position::COLUMN_DESCRIPTION.')', ':searchPhrase'),
                    )
                )
                    ->setParameter('searchPhrase', '%'.strtolower($filters['phrase']).'%');
            }
        } else {
            $queryBuilder = $queryBuilder->andWhere($queryBuilder->expr()->isNull('p.'.Position::COLUMN_DELETED_AT));
        }

        return $queryBuilder;
    }

    private function transformIncludes(array $positions, array $includes): array
    {
        $data = [];

        foreach ($positions as $position) {
            $data[] = $position->toArray();
        }

        foreach (Position::getRelations() as $relation) {
            foreach ($data as $key => $position) {
                if (!in_array($relation, $includes) || empty($includes)) {
                    unset($data[$key][$relation]);
                }
            }
        }

        return $data;
    }
}