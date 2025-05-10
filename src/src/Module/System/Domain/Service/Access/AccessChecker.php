<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Service\Access;

use App\Module\System\Domain\Interface\Access\AccessReaderInterface;

readonly class AccessChecker
{
    public function __construct(private AccessReaderInterface $accessReaderRepository)
    {
    }

    public function checkIsExists(string $accessUUID): bool
    {
        return $this->accessReaderRepository->isAccessWithUUIDExists($accessUUID);
    }

    public function checkIsActive(string $accessUUID): bool
    {
        return $this->accessReaderRepository->isAccessActive($accessUUID);
    }
}