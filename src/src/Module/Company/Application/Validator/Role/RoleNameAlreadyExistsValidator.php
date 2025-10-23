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

#[AutoconfigureTag('app.role.create.validator')]
#[AutoconfigureTag('app.role.update.validator')]
final readonly class RoleNameAlreadyExistsValidator implements ValidatorInterface
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
        if (!property_exists($data, 'name')) {
            return;
        }

        $name = $data->name;
        $roleUUID = $data->roleUUID ?? null;
        if ($this->roleReaderRepository->isRoleNameAlreadyExists($name, $roleUUID)) {
            throw new \Exception($this->translator->trans('role.name.alreadyExists', [':name' => $name], 'roles'), Response::HTTP_CONFLICT);
        }
    }
}
