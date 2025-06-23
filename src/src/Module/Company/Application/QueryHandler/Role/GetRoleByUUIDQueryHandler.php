<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Role;

use App\Module\Company\Application\Query\Role\GetRoleByUUIDQuery;
use App\Module\Company\Application\Transformer\Role\RoleDataTransformer;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class GetRoleByUUIDQueryHandler
{
    public function __construct(private RoleReaderInterface $roleReaderRepository)
    {
    }

    public function __invoke(GetRoleByUUIDQuery $query): array
    {
        $role = $this->roleReaderRepository->getRoleByUUID($query->uuid);
        $transformer = new RoleDataTransformer();

        return $transformer->transformToArray($role);
    }
}
