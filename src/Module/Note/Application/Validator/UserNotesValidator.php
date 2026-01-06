<?php

declare(strict_types=1);

namespace App\Module\Note\Application\Validator;

use App\Common\Domain\Interface\CommandInterface;
use App\Common\Domain\Interface\QueryInterface;
use App\Common\Domain\Interface\ValidatorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AutoconfigureTag('app.note.query.list.validator')]
final readonly class UserNotesValidator implements ValidatorInterface
{
    public function __construct(
        private TranslatorInterface $translator,
        private Security $security
    ) {
    }

    public function supports(CommandInterface|QueryInterface $data): bool
    {
        return true;
    }

    public function validate(CommandInterface|QueryInterface $data): void
    {
        if (!property_exists($data, 'queryDTO')) {
            return;
        }

        if (!property_exists($data->getQueryDTO(), 'userUUID')) {
            return;
        }

        $userUUIDFromQuery = $data->getQueryDTO()->userUUID;
        $loggedUser = $this->security->getUser();

        if ($userUUIDFromQuery === null) {
            throw new \Exception($this->translator->trans('note.list.user.required', [], 'notes'), Response::HTTP_CONFLICT);
        }

        if ($loggedUser === null) {
            throw new \Exception($this->translator->trans('note.list.user.forbidden', [], 'notes'), Response::HTTP_CONFLICT);
        }

        if ($userUUIDFromQuery !== $loggedUser->getUUID()->toString()) {
            throw new \Exception($this->translator->trans('note.list.user.forbidden', [], 'notes'), Response::HTTP_CONFLICT);
        }
    }
}
