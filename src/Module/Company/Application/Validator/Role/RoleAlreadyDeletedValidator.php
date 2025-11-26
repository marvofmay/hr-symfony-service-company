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

#[AutoconfigureTag('app.role.restore.validator')]
final readonly class RoleAlreadyDeletedValidator implements ValidatorInterface
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
        if (!property_exists($data, 'roleUUID')) {
            return;
        }

        $roleUUID = $data->roleUUID;
        $roleDeleted = $this->roleReaderRepository->getDeletedRoleByUUID($roleUUID);
        if (null === $roleDeleted) {
            throw new \Exception($this->translator->trans('role.deleted.notExists', [':uuid' => $roleUUID], 'roles'), Response::HTTP_NOT_FOUND);
        }
    }
}
