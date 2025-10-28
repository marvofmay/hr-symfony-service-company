<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Role;

use App\Common\Domain\Interface\CommandInterface;
use App\Common\Domain\Interface\QueryInterface;
use App\Common\Domain\Interface\ValidatorInterface;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AutoconfigureTag('app.role.delete_multiple.validator')]
final readonly class RolesExistsValidator implements ValidatorInterface
{
    public function __construct(private RoleReaderInterface $roleReaderRepository, private TranslatorInterface $translator)
    {
    }

    public function supports(CommandInterface|QueryInterface $data): bool
    {
        return true;
    }

    public function validate(CommandInterface|QueryInterface $data): void
    {
        $uuids = $data->rolesUUIDs ?? [];

        if (empty($uuids)) {
            return;
        }

        $foundRoles = $this->roleReaderRepository
            ->getRolesByUUID($uuids)
            ->map(fn ($role) => $role->getUUID())
            ->toArray();

        $missing = array_diff($uuids, $foundRoles);

        if (!empty($missing)) {
            $translatedErrors = array_map(
                fn (string $uuid) => $this->translator->trans('role.uuid.notExists', [':uuid' => $uuid], 'roles'),
                $missing
            );

            throw new \Exception(implode(', ', $translatedErrors), Response::HTTP_NOT_FOUND);
        }
    }
}
