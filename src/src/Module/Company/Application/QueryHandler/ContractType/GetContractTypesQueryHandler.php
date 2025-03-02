<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\ContractType;

use App\Module\Company\Application\Query\ContractType\GetContractTypesQuery;
use App\Module\Company\Domain\Entity\ContractType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class GetContractTypesQueryHandler
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function handle(GetContractTypesQuery $query): array
    {
        $limit = $query->getLimit();
        $orderBy = $query->getOrderBy();
        $orderDirection = $query->getOrderDirection();
        $offset = $query->getOffset();
        $filters = $query->getFilters();

        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('ct')
            ->from(ContractType::class, 'ct');

        $queryBuilder = $this->setFilters($queryBuilder, $filters);

        $totalContractTypes = count($queryBuilder->getQuery()->getResult());

        $queryBuilder = $queryBuilder->orderBy('ct.'.$orderBy, $orderDirection)
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        return [
            'totalContractTypes' => $totalContractTypes,
            'page' => $query->getPage(),
            'limit' => $query->getLimit(),
            'roles' => $queryBuilder->getQuery()->getResult(),
        ];
    }

    private function setFilters(QueryBuilder $queryBuilder, array $filters): QueryBuilder
    {
        if (!empty($filters)) {
            foreach ($filters as $fieldName => $fieldValue) {
                if (is_null($fieldValue) || in_array($fieldName, ['deleted', 'phrase'])) {
                    continue;
                }
                $queryBuilder = $queryBuilder->andWhere($queryBuilder->expr()->like('ct.'.$fieldName, ':fieldValue'))
                    ->setParameter('fieldValue', '%'.$fieldValue.'%');
            }

            if (array_key_exists('deleted', $filters)) {
                switch ($filters['deleted']) {
                    case 0:
                        $queryBuilder = $queryBuilder->andWhere($queryBuilder->expr()->isNull('ct.'.ContractType::COLUMN_DELETED_AT));
                        break;
                    case 1:
                        $this->entityManager->getFilters()->disable('soft_delete');
                        $queryBuilder = $queryBuilder->andWhere($queryBuilder->expr()->isNotNull('ct.'.ContractType::COLUMN_DELETED_AT));
                        break;
                }
            } else {
                $queryBuilder = $queryBuilder->andWhere($queryBuilder->expr()->isNull('ct.'.ContractType::COLUMN_DELETED_AT));
            }

            if (array_key_exists('phrase', $filters) && !empty($filters['phrase'])) {
                $queryBuilder = $queryBuilder->andWhere(
                    $queryBuilder->expr()->orX(
                        $queryBuilder->expr()->like('LOWER(r. '.ContractType::COLUMN_NAME.')', ':searchPhrase'),
                        $queryBuilder->expr()->like('LOWER(r.'.ContractType::COLUMN_DESCRIPTION.')', ':searchPhrase'),
                    )
                )
                ->setParameter('searchPhrase', '%'.strtolower($filters['phrase']).'%');
            }
        } else {
            $queryBuilder = $queryBuilder->andWhere($queryBuilder->expr()->isNull('ct.'.ContractType::COLUMN_DELETED_AT));
        }

        return $queryBuilder;
    }
}
