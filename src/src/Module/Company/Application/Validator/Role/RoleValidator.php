<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Role;

use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class RoleValidator
{
    public function __construct(private RoleReaderInterface  $roleReaderRepository, private TranslatorInterface $translator) {}

    public function isRoleNameAlreadyExists(string $name, ?string $uuid = null): void
    {
        if ($this->roleReaderRepository->isRoleExists($name, $uuid)) {
            throw new \Exception($this->translator->trans('role.name.alreadyExists', [':name' => $name], 'roles'), Response::HTTP_CONFLICT);
        }
    }
}