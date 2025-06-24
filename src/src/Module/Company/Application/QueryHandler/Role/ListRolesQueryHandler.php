<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Role;

use App\Common\Application\QueryHandler\ListQueryHandlerAbstract;
use App\Module\Company\Application\Event\Role\RoleListedEvent;
use App\Module\Company\Application\Event\Role\RoleMultipleDeletedEvent;
use App\Module\Company\Application\Query\Role\ListRolesQuery;
use App\Module\Company\Domain\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'query.bus')]
final class ListRolesQueryHandler extends ListQueryHandlerAbstract
{
    public function __construct(protected EntityManagerInterface $entityManager, private EventDispatcherInterface $eventDispatcher,)
    {
        parent::__construct($entityManager);
    }

    public function __invoke(ListRolesQuery $query): array
    {
        $data = $this->handle($query);
        $this->eventDispatcher->dispatch(new RoleListedEvent($query));

        return $data;
    }

    protected function getEntityClass(): string
    {
        return Role::class;
    }

    protected function getAlias(): string
    {
        return Role::ALIAS;
    }

    protected function getDefaultOrderBy(): string
    {
        return Role::COLUMN_CREATED_AT;
    }

    protected function getAllowedFilters(): array
    {
        return [
            Role::COLUMN_NAME,
            Role::COLUMN_DESCRIPTION,
            Role::COLUMN_CREATED_AT,
            Role::COLUMN_UPDATED_AT,
            Role::COLUMN_DELETED_AT,
        ];
    }

    protected function getPhraseSearchColumns(): array
    {
        return [
            Role::COLUMN_NAME,
            Role::COLUMN_DESCRIPTION,
        ];
    }

    protected function getRelations(): array
    {
        return Role::getRelations();
    }
}
