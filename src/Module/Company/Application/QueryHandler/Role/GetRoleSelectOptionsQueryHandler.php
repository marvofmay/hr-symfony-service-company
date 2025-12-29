<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Role;

use App\Module\Company\Application\Query\Role\GetRoleSelectOptionsQuery;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class GetRoleSelectOptionsQueryHandler
{
    public function __construct(
        private RoleReaderInterface $roleReaderRepository,
    ) {
    }

    public function __invoke(GetRoleSelectOptionsQuery $query): array
    {
        return $this->roleReaderRepository->getSelectOptions();
    }
}
