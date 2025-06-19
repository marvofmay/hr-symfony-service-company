<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Role;

use App\Module\Company\Application\Query\Role\GetRoleByUUIDQuery;
use App\Module\Company\Application\Transformer\Role\RoleDataTransformer;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class GetRoleByUUIDQueryHandler
{
    public function __construct(private RoleReaderInterface $roleReaderRepository, private TranslatorInterface $translator,)
    {
    }

    public function __invoke(GetRoleByUUIDQuery $query): array
    {
        $role = $this->roleReaderRepository->getRoleByUUID($query->uuid);
        if (null === $role) {
            throw new \Exception($this->translator->trans('role.uuid.notExists', [':uuid' => $query->uuid], 'roles'), Response::HTTP_NOT_FOUND);
        }

        $transformer = new RoleDataTransformer();

        return $transformer->transformToArray($role);
    }
}
