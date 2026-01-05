<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role;

use App\Module\System\Domain\Interface\Access\AccessReaderInterface;

final class AssignPermissionsReferenceLoader
{
    public array $accesses = [] {
        get {
            return $this->accesses;
        }
    }

    public function __construct(
        private readonly AccessReaderInterface $accessReaderRepository,
    ) {
    }

    public function preload(array $parsedPayload): void
    {
        $accessesUUIDs = array_keys($parsedPayload);
        $accessesUUIDs = array_unique($accessesUUIDs);
        $this->accesses = $this->mapByUUID($this->accessReaderRepository->getAccessesByUUIDs($accessesUUIDs));
    }

    private function mapByUUID(iterable $entities): array
    {
        $map = [];
        foreach ($entities as $entity) {
            $map[$entity->getUUID()->toString()] = $entity;
        }

        return $map;
    }
}
