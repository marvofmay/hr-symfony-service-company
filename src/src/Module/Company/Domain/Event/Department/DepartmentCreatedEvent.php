<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Event\Department;

use App\Common\Domain\Interface\DomainEventInterface;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\Address;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\CompanyUUID;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\Emails;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\IndustryUUID;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\NIP;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\Phones;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\REGON;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\Websites;
use App\Module\Company\Domain\Aggregate\Department\ValueObject\DepartmentUUID;

final readonly class DepartmentCreatedEvent implements DomainEventInterface
{
    public function __construct(
        public DepartmentUUID $uuid,
        public CompanyUUID $parentCompanyUUID,
        public string $name,
        public IndustryUUID $industryUUID,
        public bool $active,
        public Address $address,
        public ?Phones $phones = null,
        public ?string $description = null,
        public ?Emails $emails = null,
        public ?Websites $websites = null,
    ) {}
}