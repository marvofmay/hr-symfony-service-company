<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Industry;

use App\Module\Company\Application\Query\Industry\GetIndustriesQuery;
use App\Module\Company\Domain\Entity\Industry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class GetIndustriesQueryHandler
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function handle(GetIndustriesQuery $query): array
    {
        $limit = $query->getLimit();
        $orderBy = $query->getOrderBy();
        $orderDirection = $query->getOrderDirection();
        $offset = $query->getOffset();
        $filters = $query->getFilters();

        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('r')
            ->from(Industry::class, 'r');

        $queryBuilder = $this->setFilters($queryBuilder, $filters);

        $totalIndustries = count($queryBuilder->getQuery()->getResult());

        $queryBuilder = $queryBuilder->orderBy('r.'.$orderBy, $orderDirection)
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        return [
            'totalIndustries' => $totalIndustries,
            'page' => $query->getPage(),
            'limit' => $query->getLimit(),
            'industries' => $queryBuilder->getQuery()->getResult(),
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
                        $queryBuilder = $queryBuilder->andWhere($queryBuilder->expr()->isNull('r.'.Industry::COLUMN_DELETED_AT));
                        break;
                    case 1:
                        $this->entityManager->getFilters()->disable('soft_delete');
                        $queryBuilder = $queryBuilder->andWhere($queryBuilder->expr()->isNotNull('r.'.Industry::COLUMN_DELETED_AT));
                        break;
                }
            } else {
                $queryBuilder = $queryBuilder->andWhere($queryBuilder->expr()->isNull('r.'.Industry::COLUMN_DELETED_AT));
            }

            if (array_key_exists('phrase', $filters) && !empty($filters['phrase'])) {
                $queryBuilder = $queryBuilder->andWhere(
                    $queryBuilder->expr()->orX(
                        $queryBuilder->expr()->like('LOWER(r. '.Industry::COLUMN_NAME.')', ':searchPhrase'),
                        $queryBuilder->expr()->like('LOWER(r.'.Industry::COLUMN_DESCRIPTION.')', ':searchPhrase'),
                    )
                )
                ->setParameter('searchPhrase', '%'.strtolower($filters['phrase']).'%');
            }
        } else {
            $queryBuilder = $queryBuilder->andWhere($queryBuilder->expr()->isNull('r.'.Industry::COLUMN_DELETED_AT));
        }

        return $queryBuilder;
    }
}
