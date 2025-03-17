<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Employee;

use App\Module\Company\Application\Query\Employee\GetEmployeesQuery;
use App\Module\Company\Domain\Entity\Employee;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class GetEmployeesQueryHandler
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function handle(GetEmployeesQuery $query): array
    {
        $limit = $query->getLimit();
        $orderBy = $query->getOrderBy();
        $orderDirection = $query->getOrderDirection();
        $offset = $query->getOffset();
        $filters = $query->getFilters();
        $includes = $query->getIncludes();

        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('e')
            ->from(Employee::class, 'e');

        $queryBuilder = $this->setFilters($queryBuilder, $filters);

        $totalEmployees = count($queryBuilder->getQuery()->getResult());

        //ToDo:: Refactor query - use left join with companyUUID, departmentUUID, employeeUUID, roleUUID, positionUUID, contractTypeUUID
        $queryBuilder = $queryBuilder->orderBy('e.'.$orderBy, $orderDirection)
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        $employees = $queryBuilder->getQuery()->getResult();

        return [
            'totalEmployees' => $totalEmployees,
            'page' => $query->getPage(),
            'limit' => $query->getLimit(),
            'employees' => $this->transformIncludes($employees, $includes),
        ];
    }

    private function setFilters(QueryBuilder $queryBuilder, array $filters): QueryBuilder
    {
        if (!empty($filters)) {
            foreach ($filters as $fieldName => $fieldValue) {
                if (is_null($fieldValue) || in_array($fieldName, ['deleted', 'phrase'])) {
                    continue;
                }
                $queryBuilder = $queryBuilder->andWhere($queryBuilder->expr()->like('e.'.$fieldName, ':fieldValue'))
                    ->setParameter('fieldValue', '%'.$fieldValue.'%');
            }

            if (array_key_exists('deleted', $filters)) {
                switch ($filters['deleted']) {
                    case 0:
                        $queryBuilder = $queryBuilder->andWhere($queryBuilder->expr()->isNull('e.'.Employee::COLUMN_DELETED_AT));
                        break;
                    case 1:
                        $this->entityManager->getFilters()->disable('soft_delete');
                        $queryBuilder = $queryBuilder->andWhere($queryBuilder->expr()->isNotNull('e.'.Employee::COLUMN_DELETED_AT));
                        break;
                }
            } else {
                $queryBuilder = $queryBuilder->andWhere($queryBuilder->expr()->isNull('e.'.Employee::COLUMN_DELETED_AT));
            }

            if (array_key_exists('phrase', $filters) && !empty($filters['phrase'])) {
                $queryBuilder = $queryBuilder->andWhere(
                    $queryBuilder->expr()->orX(
                        $queryBuilder->expr()->like('LOWER(e. '.Employee::COLUMN_FIRST_NAME.')', ':searchPhrase'),
                        $queryBuilder->expr()->like('LOWER(e.'.Employee::COLUMN_LAST_NAME.')', ':searchPhrase'),
                    )
                )
                ->setParameter('searchPhrase', '%'.strtolower($filters['phrase']).'%');
            }
        } else {
            $queryBuilder = $queryBuilder->andWhere($queryBuilder->expr()->isNull('e.'.Employee::COLUMN_DELETED_AT));
        }

        return $queryBuilder;
    }

    private function transformIncludes(array $employees, array $includes): array
    {
        $data = [];

        foreach ($employees as $employee) {
            $data[] = $employee->toArray();
        }

        foreach (Employee::getRelations() as $relation) {
            foreach ($data as $key => $employee) {
                if (!in_array($relation, $includes) || empty($includes)) {
                    unset($data[$key][$relation]);
                }
            }
        }

        return $data;
    }
}
