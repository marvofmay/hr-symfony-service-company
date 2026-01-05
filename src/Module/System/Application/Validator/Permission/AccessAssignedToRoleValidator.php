<?php

declare(strict_types=1);

namespace App\Module\System\Application\Validator\Permission;

use App\Common\Domain\Interface\CommandInterface;
use App\Common\Domain\Interface\QueryInterface;
use App\Common\Domain\Interface\ValidatorInterface;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use App\Module\Company\Domain\Service\Role\AssignPermissionsPayloadParser;

;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AutoconfigureTag('app.role.assignPermissions.validator')]
final readonly class AccessAssignedToRoleValidator implements ValidatorInterface
{
    public function __construct(
        private RoleReaderInterface $roleReaderRepository,
        private AssignPermissionsPayloadParser $assignPermissionsPayloadParser,
        private TranslatorInterface $translator
    ) {
    }

    public function supports(CommandInterface|QueryInterface $data): bool
    {
        return true;
    }

    public function validate(CommandInterface|QueryInterface $data): void
    {
        $roleUUID = $data->roleUUID;
        $role = $this->roleReaderRepository->getRoleByUUID($roleUUID);

        $parsedPayload = $this->assignPermissionsPayloadParser->parse($data);

        $accessesUUIDs = array_keys($parsedPayload);
        $accessesUUIDs = array_unique($accessesUUIDs);

        if (empty($accessesUUIDs)) {
            return;
        }

        $roleAccesses = $role->getAccesses();
        $currentAssignedRoleAccesses = [];
        foreach ($roleAccesses as $roleAccess) {
            $currentAssignedRoleAccesses[] = $roleAccess->getUUID()->toString();
        }


        $notAssignedAccesses = array_diff($accessesUUIDs, $currentAssignedRoleAccesses);
        if (!empty($notAssignedAccesses)) {
            $message = $this->translator->trans(
                'role.access.notAssigned',
                [':accesses' => implode(', ', $notAssignedAccesses), ':role' => $role->getName()],
                'roles'
            );

            throw new \Exception($message, Response::HTTP_NOT_FOUND);
        }


    }
}
