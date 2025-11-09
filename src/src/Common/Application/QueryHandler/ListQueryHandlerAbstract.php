<?php

declare(strict_types=1);

namespace App\Common\Application\QueryHandler;

use App\Common\Application\Factory\TransformerFactory;
use App\Common\Domain\Interface\ListQueryHandlerInterface;
use App\Common\Domain\Interface\ListQueryInterface;
use App\Common\Domain\Interface\QueryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

abstract class ListQueryHandlerAbstract implements ListQueryHandlerInterface
{
    protected iterable $validators = [];

    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected TransformerFactory $transformerFactory,
    )
    {
    }

    abstract public function getEntityClass(): string;

    abstract public function getAlias(): string;

    abstract public function getDefaultOrderBy(): string;

    abstract public function getAllowedFilters(): array;

    abstract public function getRelations(): array;

    public function handle(ListQueryInterface $query): array
    {
        $queryBuilder = $this->createBaseQueryBuilder();
        $queryBuilder = $this->setFilters($queryBuilder, $query->getFilters());

        $entityClass = $queryBuilder->getRootEntities()[0];
        $metadata = $this->entityManager->getClassMetadata($entityClass);
        $identifierField = $metadata->getSingleIdentifierFieldName();

        $totalItems = (clone $queryBuilder)
            ->resetDQLPart('orderBy')
            ->select(sprintf('COUNT(%s.%s)', $this->getAlias(), $identifierField))
            ->getQuery()
            ->getSingleScalarResult();


        foreach ($query->getIncludes() as $relation) {
            if (in_array($relation, $this->getRelations(), true)) {
                $queryBuilder
                    ->leftJoin(sprintf('%s.%s', $this->getAlias(), $relation), $relation)
                    ->addSelect($relation);
            }
        }


        $orderByField = $query->getOrderBy() ?? $this->getDefaultOrderBy();
        if (!str_contains($orderByField, '.')) {
            $orderByField = sprintf('%s.%s', $this->getAlias(), $orderByField);
        }

        $queryBuilder
            ->orderBy($orderByField, $query->getOrderDirection())
            ->setMaxResults($query->getLimit())
            ->setFirstResult($query->getOffset());


        $items = $queryBuilder->getQuery()->getResult();

        return [
            'total' => (int) $totalItems,
            'page' => $query->getPage(),
            'limit' => $query->getLimit(),
            'items' => $this->transformIncludes($items, $query->getIncludes()),
        ];
    }

    public function createBaseQueryBuilder(): QueryBuilder
    {
        return $this->entityManager->getRepository($this->getEntityClass())->createQueryBuilder($this->getAlias());
    }

    abstract public function getPhraseSearchColumns(): array;

    public function setFilters(QueryBuilder $queryBuilder, array $filters): QueryBuilder
    {
        $alias = $this->getAlias();
        $allowedFilters = $this->getAllowedFilters();

        foreach ($filters as $fieldName => $fieldValue) {
            if (!in_array($fieldName, $allowedFilters, true)) {
                continue;
            }

            // przypadek: filtr po relacji "employee"
            if ($fieldName === 'employee') {
                if ($fieldValue === 'null' || $fieldValue === null) {
                    $queryBuilder->andWhere("$alias.employee IS NULL");
                } else {
                    if (!in_array('employee', $queryBuilder->getAllAliases(), true)) {
                        $queryBuilder->leftJoin("$alias.employee", 'employee');
                    }

                    $queryBuilder
                        ->andWhere('employee.uuid = :employeeUuid')
                        ->setParameter('employeeUuid', $fieldValue);
                }

                continue;
            }

            if (!is_null($fieldValue)) {
                $queryBuilder
                    ->andWhere($queryBuilder->expr()->like("$alias.$fieldName", ':'.$fieldName))
                    ->setParameter($fieldName, "%$fieldValue%");
            }
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

    public function getTransformer(): object
    {
        //return TransformerFactory::createForHandler(static::class);
        return $this->transformerFactory->createForHandler(static::class);
    }

    public function transformIncludes(array $items, array $includes): array
    {
        $transformer = $this->getTransformer();

        return array_map(fn ($item) => $this->transformItem($transformer, $item, $includes), $items);
    }

    public function transformItem($transformer, $item, array $includes): array
    {
        return method_exists($transformer, 'transformToArray')
            ? $transformer->transformToArray($item, $includes)
            : throw new \RuntimeException('Transformer does not implement transformToArray method.');
    }

    protected function validate(QueryInterface $query): void
    {
        foreach ($this->validators as $validator) {
            if (method_exists($validator, 'supports') && $validator->supports($query)) {
                $validator->validate($query);
            }
        }
    }
}
