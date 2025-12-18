<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Department;

use App\Common\Application\Factory\TransformerFactory;
use App\Common\Application\QueryHandler\ListQueryHandlerAbstract;
use App\Module\Company\Application\Event\Department\DepartmentListedEvent;
use App\Module\Company\Application\Query\Department\ListDepartmentsQuery;
use App\Module\Company\Domain\Entity\Department;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'query.bus')]
final class ListDepartmentsQueryHandler extends ListQueryHandlerAbstract
{
    public function __construct(
        public EntityManagerInterface $entityManager,
        protected TransformerFactory $transformerFactory,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($entityManager, $transformerFactory);
    }

    public function __invoke(ListDepartmentsQuery $query): array
    {
        $this->eventDispatcher->dispatch(new DepartmentListedEvent([$query]));

        return $this->handle($query);
    }

    public function getEntityClass(): string
    {
        return Department::class;
    }

    public function getAlias(): string
    {
        return Department::ALIAS;
    }

    public function getDefaultOrderBy(): string
    {
        return Department::COLUMN_CREATED_AT;
    }

    public function getAllowedFilters(): array
    {
        return [
            Department::COLUMN_NAME,
            Department::COLUMN_DESCRIPTION,
            Department::COLUMN_ACTIVE,
            Department::COLUMN_CREATED_AT,
            Department::COLUMN_UPDATED_AT,
            Department::COLUMN_DELETED_AT,
            Department::RELATION_COMPANY,
        ];
    }

    public function getPhraseSearchColumns(): array
    {
        return [
            Department::COLUMN_NAME,
            Department::COLUMN_DESCRIPTION,
        ];
    }

    public function getRelations(): array
    {
        return Department::getRelations();
    }
}
