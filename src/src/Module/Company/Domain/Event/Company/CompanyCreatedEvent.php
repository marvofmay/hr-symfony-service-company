<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Event\Company;

use App\Common\Domain\Interface\DomainEventInterface;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\Address;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\CompanyUUID;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\Emails;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\FullName;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\IndustryUUID;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\NIP;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\Phones;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\REGON;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\ShortName;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\Websites;

final readonly class CompanyCreatedEvent implements DomainEventInterface
{
    public function __construct(
        public CompanyUUID $uuid,
        public FullName $fullName,
        public NIP $nip,
        public REGON $regon,
        public IndustryUUID $industryUUID,
        public bool $active,
        public Address $address,
        public Phones $phones,
        public ?ShortName $shortName = null,
        public ?string $description = null,
        public ?CompanyUUID $parentCompanyUUID = null,
        public ?Emails $emails = null,
        public ?Websites $websites = null,
    ) {}
}