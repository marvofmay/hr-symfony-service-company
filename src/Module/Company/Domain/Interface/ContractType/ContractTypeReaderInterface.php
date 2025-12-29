<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\ContractType;

use App\Module\Company\Domain\Entity\ContractType;
use Doctrine\Common\Collections\Collection;

interface ContractTypeReaderInterface
{
    public function getContractTypeByUUID(string $uuid): ?ContractType;

    public function getContractTypesByUUIDs(array $contractTypesUUIDs): Collection;

    public function getContractTypeByName(string $name, ?string $uuid): ?ContractType;

    public function isContractTypeNameAlreadyExists(string $name, ?string $uuid = null): bool;
    public function isContractTypeWithUUIDExists(string $uuid): bool;
    public function getDeletedContractTypeByUUID(string $uuid): ?ContractType;
    public function getContractTypesByNames(array $names): Collection;
    public function getSelectOptions(bool $onlyActive = true): array;
}
