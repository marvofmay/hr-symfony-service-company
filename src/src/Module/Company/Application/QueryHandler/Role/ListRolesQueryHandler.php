<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Role;

use App\Common\Application\QueryHandler\ListQueryHandlerAbstract;
use App\Module\Company\Application\Event\Role\RoleListedEvent;
use App\Module\Company\Application\Query\Role\ListRolesQuery;
use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Enum\Role\RoleEntityFieldEnum;
use App\Module\Company\Domain\Enum\TimeStampableEntityFieldEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'query.bus')]
final class ListRolesQueryHandler extends ListQueryHandlerAbstract
{
    public function __construct(
        public EntityManagerInterface $entityManager,
        private readonly EventDispatcherInterface $eventDispatcher
    )
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
        return TimeStampableEntityFieldEnum::CREATED_AT->value;
    }

    public function getAllowedFilters(): array
    {
        return [
            RoleEntityFieldEnum::NAME->value,
            RoleEntityFieldEnum::DESCRIPTION->value,
            TimeStampableEntityFieldEnum::CREATED_AT->value,
            TimeStampableEntityFieldEnum::UPDATED_AT->value,
            TimeStampableEntityFieldEnum::UPDATED_AT->value,
        ];
    }

    public function getPhraseSearchColumns(): array
    {
        return [
            RoleEntityFieldEnum::NAME->value,
            RoleEntityFieldEnum::DESCRIPTION->value,
        ];
    }

    public function getRelations(): array
    {
        return Role::getRelations();
    }
}
