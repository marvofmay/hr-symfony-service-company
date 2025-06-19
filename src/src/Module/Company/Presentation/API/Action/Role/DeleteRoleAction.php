<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Role;

use App\Module\Company\Application\Command\Role\DeleteRoleCommand;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class DeleteRoleAction
{
    public function __construct(private MessageBusInterface $commandBus, private RoleReaderInterface $roleReaderRepository, private TranslatorInterface $translator,)
    {}

    public function execute(string $uuid): void
    {
        try {
            $role = $this->roleReaderRepository->getRoleByUUID($uuid);
            if (null === $role) {
                throw new \Exception($this->translator->trans('role.uuid.notExists', [':uuid' => $uuid], 'roles'), Response::HTTP_NOT_FOUND);
            }

            $this->commandBus->dispatch(new DeleteRoleCommand($role));
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }
    }
}
