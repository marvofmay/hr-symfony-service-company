<?php

declare(strict_types=1);

namespace App\Module\Note\Application\QueryHandler;

use App\Module\Note\Application\Query\GetNotesQuery;
use App\Module\Note\Domain\Entity\Note;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class GetNotesQueryHandler
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    public function handle(GetNotesQuery $query): array
    {
        $limit = $query->getLimit();
        $orderBy = $query->getOrderBy();
        $orderDirection = $query->getOrderDirection();
        $offset = $query->getOffset();
        $filters = $query->getFilters();
        $includes = $query->getIncludes();

        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('n')
            ->from(Note::class, 'n');

        $queryBuilder = $this->setFilters($queryBuilder, $filters);

        $totalNotes = count($queryBuilder->getQuery()->getResult());

        $queryBuilder = $queryBuilder->orderBy('n.' . $orderBy, $orderDirection)
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        $positions = $queryBuilder->getQuery()->getResult();

        return [
            'totalNotes' => $totalNotes,
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

                $queryBuilder = $queryBuilder->andWhere($queryBuilder->expr()->like('n.'.$fieldName, ':fieldValue'))
                    ->setParameter('fieldValue', '%'.$fieldValue.'%');
            }

            if (array_key_exists('deleted', $filters)) {
                switch ($filters['deleted']) {
                    case 0:
                        $queryBuilder = $queryBuilder->andWhere($queryBuilder->expr()->isNull('n.'.Note::COLUMN_DELETED_AT));
                        break;
                    case 1:
                        $this->entityManager->getFilters()->disable('soft_delete');
                        $queryBuilder = $queryBuilder->andWhere($queryBuilder->expr()->isNotNull('n.'.Note::COLUMN_DELETED_AT));
                        break;
                }
            } else {
                $queryBuilder = $queryBuilder->andWhere($queryBuilder->expr()->isNull('n.'.Note::COLUMN_DELETED_AT));
            }

            if (array_key_exists('phrase', $filters) && !empty($filters['phrase'])) {
                $queryBuilder = $queryBuilder->andWhere(
                    $queryBuilder->expr()->orX(
                        $queryBuilder->expr()->like('LOWER(p.'.Note::COLUMN_TITLE.')', ':searchPhrase'),
                        $queryBuilder->expr()->like('LOWER(p.'.Note::COLUMN_CONTENT.')', ':searchPhrase'),
                        $queryBuilder->expr()->like('LOWER(p.'.Note::COLUMN_PRIORITY.')', ':searchPhrase'),
                    )
                )
                    ->setParameter('searchPhrase', '%'.strtolower($filters['phrase']).'%');
            }
        } else {
            $queryBuilder = $queryBuilder->andWhere($queryBuilder->expr()->isNull('n.'.Note::COLUMN_DELETED_AT));
        }

        return $queryBuilder;
    }

    private function transformIncludes(array $positions, array $includes): array
    {
        $data = [];

        foreach ($positions as $position) {
            $data[] = $position->toArray();
        }

        foreach (Note::getRelations() as $relation) {
            foreach ($data as $key => $position) {
                if (!in_array($relation, $includes) || empty($includes)) {
                    unset($data[$key][$relation]);
                }
            }
        }

        return $data;
    }
}