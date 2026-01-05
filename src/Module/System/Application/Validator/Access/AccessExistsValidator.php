<?php

declare(strict_types=1);

namespace App\Module\System\Application\Validator\Access;

use App\Common\Domain\Interface\CommandInterface;
use App\Common\Domain\Interface\QueryInterface;
use App\Common\Domain\Interface\ValidatorInterface;
use App\Module\System\Domain\Interface\Access\AccessReaderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class AccessExistsValidator implements ValidatorInterface
{
    public function __construct(
        private AccessReaderInterface $accessReaderRepository,
        private TranslatorInterface $translator
    ) {
    }

    public function supports(CommandInterface|QueryInterface $data): bool
    {
        return true;
    }

    public function validate(CommandInterface|QueryInterface $data): void
    {
        if (!property_exists($data, 'accessUUID')) {
            return;
        }

        $accessUUID = $data->accessUUID;
        $accessExists = $this->accessReaderRepository->isAccessWithUUIDExists($accessUUID);
        if (!$accessExists) {
            throw new \Exception($this->translator->trans('access.uuid.notExists', [':uuid' => $accessUUID], 'accesses'), Response::HTTP_CONFLICT);
        }
    }
}
