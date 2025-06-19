<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\RoleAccessPermission;

use App\Module\Company\Domain\Entity\Role;
use App\Module\System\Domain\Entity\RoleAccessPermission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class RoleAccessPermissionValidator
{
    public function __construct(private EntityManagerInterface $entityManager, private TranslatorInterface $translator,)
    {
    }

    public function isPermissionAlreadyAssignedToRoleAccess(Role $role, array $accessUuids, array $permissionUuids): void
    {
        if (empty($accessUuids) || empty($permissionUuids)) {
            return;
        }

        $qb = $this->entityManager->createQueryBuilder();

        $qb->select('rap')
            ->from(RoleAccessPermission::class, 'rap')
            ->join('rap.access', 'a')
            ->join('rap.permission', 'p')
            ->where('rap.role = :role')
            ->andWhere('a.uuid IN (:accessUuids)')
            ->andWhere('p.uuid IN (:permissionUuids)')
            ->setParameter('role', $role)
            ->setParameter('accessUuids', $accessUuids)
            ->setParameter('permissionUuids', $permissionUuids);

        $existing = $qb->getQuery()->getResult();

        if (!empty($existing)) {
            $messages = [];

            foreach ($existing as $rap) {
                $accessName = $rap->getAccess()->getName();
                $permissionName = $rap->getPermission()->getName();

                $messages[] = sprintf('%s - %s - %s', $role->getName(), $accessName, $permissionName);
            }

            $message = $this->translator->trans('role.accesses.permissions.alreadyExists', [':items' => implode(', ', $messages),], 'roles');

            throw new \Exception($message, Response::HTTP_CONFLICT);
        }
    }
}