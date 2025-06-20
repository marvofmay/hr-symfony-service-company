<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Department;

use App\Module\Company\Application\Query\Department\GetDepartmentsQuery;
use App\Module\Company\Domain\Entity\Department;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class GetDepartmentsQueryHandler
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function handle(GetDepartmentsQuery $query): array
    {
        $limit = $query->getLimit();
        $orderBy = $query->getOrderBy();
        $orderDirection = $query->getOrderDirection();
        $offset = $query->getOffset();
        $filters = $query->getFilters();
        $includes = $query->getIncludes();

        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('r')
            ->from(Department::class, 'r');

        $queryBuilder = $this->setFilters($queryBuilder, $filters);

        $totalDepartments = count($queryBuilder->getQuery()->getResult());

        // ToDo:: Refactor query - use left join with companyUUID, departmentUUID
        $queryBuilder = $queryBuilder->orderBy('r.'.$orderBy, $orderDirection)
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        $departments = $queryBuilder->getQuery()->getResult();

        return [
            'totalDepartments' => $totalDepartments,
            'page' => $query->getPage(),
            'limit' => $query->getLimit(),
            'departments' => $this->transformIncludes($departments, $includes),
        ];
    }

    private function setFilters(QueryBuilder $queryBuilder, array $filters): QueryBuilder
    {
        if (!empty($filters)) {
            foreach ($filters as $fieldName => $fieldValue) {
                if (is_null($fieldValue) || in_array($fieldName, ['deleted', 'phrase'])) {
                    continue;
                }
                $queryBuilder = $queryBuilder->andWhere($queryBuilder->expr()->like('r.'.$fieldName, ':fieldValue'))
                    ->setParameter('fieldValue', '%'.$fieldValue.'%');
            }

            if (array_key_exists('deleted', $filters)) {
                switch ($filters['deleted']) {
                    case 0:
                        $queryBuilder = $queryBuilder->andWhere($queryBuilder->expr()->isNull('r.'.Department::COLUMN_DELETED_AT));
                        break;
                    case 1:
                        $this->entityManager->getFilters()->disable('soft_delete');
                        $queryBuilder = $queryBuilder->andWhere($queryBuilder->expr()->isNotNull('r.'.Department::COLUMN_DELETED_AT));
                        break;
                }
            } else {
                $queryBuilder = $queryBuilder->andWhere($queryBuilder->expr()->isNull('r.'.Department::COLUMN_DELETED_AT));
            }

            if (array_key_exists('phrase', $filters) && !empty($filters['phrase'])) {
                $queryBuilder = $queryBuilder->andWhere(
                    $queryBuilder->expr()->orX(
                        $queryBuilder->expr()->like('LOWER(r. '.Department::COLUMN_NAME.')', ':searchPhrase'),
                        $queryBuilder->expr()->like('LOWER(r.'.Department::COLUMN_DESCRIPTION.')', ':searchPhrase'),
                    )
                )
                ->setParameter('searchPhrase', '%'.strtolower($filters['phrase']).'%');
            }
        } else {
            $queryBuilder = $queryBuilder->andWhere($queryBuilder->expr()->isNull('r.'.Department::COLUMN_DELETED_AT));
        }

        return $queryBuilder;
    }

    private function transformIncludes(array $departments, array $includes): array
    {
        $data = [];

        foreach ($departments as $department) {
            $data[] = $department->toArray();
        }

        foreach (Department::getRelations() as $relation) {
            foreach ($data as $key => $department) {
                if (!in_array($relation, $includes) || empty($includes)) {
                    unset($data[$key][$relation]);
                }
            }
        }

        return $data;
    }
}
