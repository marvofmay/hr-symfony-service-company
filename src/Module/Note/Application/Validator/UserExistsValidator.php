<?php

declare(strict_types=1);

namespace App\Module\Note\Application\Validator;

use App\Common\Domain\Interface\CommandInterface;
use App\Common\Domain\Interface\QueryInterface;
use App\Common\Domain\Interface\ValidatorInterface;
use App\Module\Company\Domain\Interface\User\UserReaderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AutoconfigureTag('app.note.query.list.validator')]
final readonly class UserExistsValidator implements ValidatorInterface
{
    public function __construct(
        private UserReaderInterface $userReaderRepository,
        private TranslatorInterface $translator
    ) {
    }

    public function supports(CommandInterface|QueryInterface $data): bool
    {
        $userUUID = $data->getQueryDTO()->user;

        return is_string($userUUID) && $userUUID !== 'null';

    }

    public function validate(CommandInterface|QueryInterface $data): void
    {
        if (!property_exists($data, 'queryDTO')) {
            return;
        }

        if (!property_exists($data->getQueryDTO(), 'user')) {
            return;
        }

        $userUUID = $data->getQueryDTO()->user;

        if (!preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-5][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$/', $userUUID)) {
            throw new \Exception($this->translator->trans('uuid.invalid', [], 'validators'), Response::HTTP_CONFLICT);
        }

        $user = $this->userReaderRepository->getUserByUUID($userUUID);
        if (null === $user) {
            throw new \Exception($this->translator->trans('user.uuid.notExists', [':uuid' => $userUUID], 'users'), Response::HTTP_CONFLICT);
        }
    }
}
