<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Event\Company;

use App\Common\Domain\Interface\DomainEventInterface;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\Address;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\CompanyUUID;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\IndustryUUID;

final readonly class CompanyCreatedEvent implements DomainEventInterface
{
    public \DateTimeImmutable $occurredAt;

    public function __construct(
        public CompanyUUID $uuid,
        public string $fullName,
        public string $nip,
        public string $regon,
        public IndustryUUID $industryUUID,
        public bool $active,
        public Address $address,
        public ?string $shortName = null,
        public ?string $description = null,
        public ?CompanyUUID $parentCompanyUUID = null,
    ) {}

    public function getOccurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
}