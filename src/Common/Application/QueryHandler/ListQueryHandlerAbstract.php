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
    ) {
    }

    abstract public function getEntityClass(): string;

    abstract public function getAlias(): string;

    abstract public function getDefaultOrderBy(): string;

    abstract public function getAllowedFilters(): array;

    abstract public function getRelations(): array;

    abstract public function getPhraseSearchColumns(): array;

    public function handle(ListQueryInterface $query): array
    {
        $alias = $this->getAlias();

        $baseQB = $this->createBaseQueryBuilder();
        $baseQB = $this->setFilters($baseQB, $query->getFilters());

        $entityClass = $baseQB->getRootEntities()[0];
        $metadata = $this->entityManager->getClassMetadata($entityClass);
        $identifier = $metadata->getSingleIdentifierFieldName();

        $total = (clone $baseQB)
            ->resetDQLPart('orderBy')
            ->select("COUNT($alias.$identifier)")
            ->getQuery()
            ->getSingleScalarResult();

        $orderByField = $query->getOrderBy() ?? $this->getDefaultOrderBy();
        if (!str_contains($orderByField, '.')) {
            $orderByField = "$alias.$orderByField";
        }

        $ids = (clone $baseQB)
            ->select("$alias.$identifier")
            ->orderBy($orderByField, $query->getOrderDirection())
            ->setFirstResult($query->getOffset())
            ->setMaxResults($query->getLimit())
            ->getQuery()
            ->getScalarResult();

        if (empty($ids)) {
            return [
                'total' => (int)$total,
                'page'  => $query->getPage(),
                'limit' => $query->getLimit(),
                'items' => [],
            ];
        }

        $ids = array_map(fn ($row) => array_values($row)[0], $ids);

        $qb = $this->createBaseQueryBuilder();
        $qb->andWhere($qb->expr()->in("$alias.$identifier", ':ids'))
            ->setParameter('ids', $ids);

        foreach ($query->getIncludes() as $relation) {
            if (in_array($relation, $this->getRelations(), true)) {
                $qb->leftJoin("$alias.$relation", $relation)
                    ->addSelect($relation);
            }
        }

        $qb->orderBy("$alias.$identifier", 'ASC');

        $items = $qb->getQuery()->getResult();

        return [
            'total' => (int)$total,
            'page'  => $query->getPage(),
            'limit' => $query->getLimit(),
            'items' => $this->transformIncludes($items, $query->getIncludes()),
        ];
    }

    public function createBaseQueryBuilder(): QueryBuilder
    {
        return $this->entityManager
            ->getRepository($this->getEntityClass())
            ->createQueryBuilder($this->getAlias());
    }

    public function setFilters(QueryBuilder $qb, array $filters): QueryBuilder
    {
        $alias = $this->getAlias();
        $allowed = $this->getAllowedFilters();

        foreach ($filters as $field => $value) {

            if (!in_array($field, $allowed, true)) {
                continue;
            }

            if ($field === 'user') {
                if (!in_array('user', $qb->getAllAliases(), true)) {
                    $qb->leftJoin("$alias.user", 'user');
                }
                $qb->andWhere('user.uuid = :userUUID')
                    ->setParameter('userUUID', $value);
                continue;
            }

            if (!is_null($value)) {
                $qb->andWhere($qb->expr()->like("$alias.$field", ':' . $field))
                    ->setParameter($field, '%' . $value . '%');
            }
        }

        if (array_key_exists('deleted', $filters)) {
            $col = "$alias.deletedAt";
            switch ($filters['deleted']) {
                case 0:
                    $qb->andWhere($qb->expr()->isNull($col));
                    break;

                case 1:
                    $this->entityManager->getFilters()->disable('soft_delete');
                    $qb->andWhere($qb->expr()->isNotNull($col));
                    break;
            }
        } else {
            $qb->andWhere($qb->expr()->isNull("$alias.deletedAt"));
        }

        if (!empty($filters['phrase'])) {
            $columns = $this->getPhraseSearchColumns();
            $expr = $qb->expr();
            $or = [];

            foreach ($columns as $col) {
                $or[] = $expr->like("LOWER($alias.$col)", ':phrase');
            }

            if ($or) {
                $qb->andWhere(call_user_func_array([$expr, 'orX'], $or))
                    ->setParameter('phrase', '%' . strtolower($filters['phrase']) . '%');
            }
        }

        return $qb;
    }

    public function getTransformer(): object
    {
        return $this->transformerFactory->createForHandler(static::class);
    }

    public function transformIncludes(array $items, array $includes): array
    {
        $transformer = $this->getTransformer();
        return array_map(
            fn ($item) => $this->transformItem($transformer, $item, $includes),
            $items
        );
    }

    public function transformItem($transformer, $item, array $includes): array
    {
        if (!method_exists($transformer, 'transformToArray')) {
            throw new \RuntimeException('Transformer must implement transformToArray()');
        }

        return $transformer->transformToArray($item, $includes);
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
