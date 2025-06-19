<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\RoleAccess;

use App\Module\Company\Domain\Entity\Role;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class RoleAccessValidator
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function isAccessesAlreadyAssignedToRole(Role $role, Collection $accesses): void
    {
        $existingAccesses = [];
        $currentAccesses = $role->getAccesses();

        foreach ($accesses as $access) {
            if ($currentAccesses->contains($access)) {
                $existingAccesses[] = $access->getName();
            }
        }

        if (count($existingAccesses) > 0) {
            throw new \Exception($this->translator->trans('role.accesses.alreadyExists', [':role' => $role->getName(), ':accesses' => implode(',', $existingAccesses)], 'roles'), Response::HTTP_CONFLICT);
        }
    }
}