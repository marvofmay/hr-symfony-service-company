<?php

declare(strict_types = 1);

namespace App\Application\QueryHandler\Role;

use App\Application\Query\Role\GetRolesQuery;
use App\Domain\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class GetRolesQueryHandler
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    public function handle(GetRolesQuery $query): array
    {
        $limit = $query->getLimit();
        $orderBy = $query->getOrderBy();
        $orderDirection = $query->getOrderDirection();
        $offset = $query->getOffset();
        $filters = $query->getFilters();

        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('r')
            ->from(Role::class, 'r');

        $queryBuilder = $this->setFilters($queryBuilder, $filters);

        $totalRoles = count($queryBuilder->getQuery()->getResult());

        $queryBuilder = $queryBuilder->orderBy('r.' . $orderBy, $orderDirection)
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        return [
            'totalRoles' => $totalRoles,
            'page' => $query->getPage(),
            'limit' => $query->getLimit(),
            'roles' => $queryBuilder->getQuery()->getResult(),
        ];
    }

    private function setFilters (QueryBuilder $queryBuilder, array $filters): QueryBuilder
    {
        if (! empty($filters)) {
            foreach ($filters as $fieldName => $fieldValue) {
                if (in_array($fieldName, ['deleted', 'phrase'])) {
                    continue;
                }
                $queryBuilder = $queryBuilder->andWhere($queryBuilder->expr()->like('r.' . $fieldName, ':fieldValue'))
                    ->setParameter('fieldValue', '%' . $fieldValue . '%');
            }

            if (array_key_exists('deleted', $filters)) {
                switch ($filters['deleted']) {
                    case 0:
                        $queryBuilder = $queryBuilder->andWhere($queryBuilder->expr()->isNull('r.' . Role::COLUMN_DELETED_AT));
                        break;
                    case 1:
                        $queryBuilder = $queryBuilder->andWhere($queryBuilder->expr()->isNotNull('r.' . Role::COLUMN_DELETED_AT));
                        break;
                }
            }

            if (array_key_exists('phrase', $filters)) {
                $queryBuilder = $queryBuilder->andWhere(
                    $queryBuilder->expr()->orX(
                        $queryBuilder->expr()->like('LOWER(r. ' . Role::COLUMN_NAME . ')', ':searchPhrase'),
                        $queryBuilder->expr()->like('LOWER(r.' . Role::COLUMN_DESCRIPTION . ')', ':searchPhrase'),
                    )
                )
                ->setParameter('searchPhrase', '%' . strtolower($filters['phrase']) . '%');
            }
        }

        return $queryBuilder;
    }
}
