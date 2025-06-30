<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Role;

use App\Common\Application\QueryHandler\ListQueryHandlerAbstract;
use App\Module\Company\Application\Event\Role\RoleListedEvent;
use App\Module\Company\Application\Query\Role\ListRolesQuery;
use App\Module\Company\Domain\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'query.bus')]
final class ListRolesQueryHandler extends ListQueryHandlerAbstract
{
    public function __construct(public EntityManagerInterface $entityManager, private EventDispatcherInterface $eventDispatcher,)
    {
        parent::__construct($entityManager);
    }

    public function __invoke(ListRolesQuery $query): array
    {
        $this->eventDispatcher->dispatch(new RoleListedEvent([$query]));

        return $this->handle($query);
    }

    public function getEntityClass(): string
    {
        return Role::class;
    }

    public function getAlias(): string
    {
        return Role::ALIAS;
    }

    public function getDefaultOrderBy(): string
    {
        return Role::COLUMN_CREATED_AT;
    }

    public function getAllowedFilters(): array
    {
        return [
            Role::COLUMN_NAME,
            Role::COLUMN_DESCRIPTION,
            Role::COLUMN_CREATED_AT,
            Role::COLUMN_UPDATED_AT,
            Role::COLUMN_DELETED_AT,
        ];
    }

    public function getPhraseSearchColumns(): array
    {
        return [
            Role::COLUMN_NAME,
            Role::COLUMN_DESCRIPTION,
        ];
    }

    public function getRelations(): array
    {
        return Role::getRelations();
    }
}
