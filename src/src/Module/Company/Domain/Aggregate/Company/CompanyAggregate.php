<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Aggregate\Company;

use App\Common\Domain\Abstract\AbstractAggregateRoot;
use App\Common\Domain\Interface\DomainEventInterface;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\Address;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\CompanyUUID;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\Emails;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\IndustryUUID;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\NIP;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\Phones;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\REGON;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\Websites;
use App\Module\Company\Domain\Event\Company\CompanyCreatedEvent;
use App\Module\Company\Domain\Event\Company\CompanyUpdatedEvent;

class CompanyAggregate extends AbstractAggregateRoot
{
    private CompanyUUID $uuid;
    private ?CompanyUUID $parentCompanyUUID = null;
    private IndustryUUID $industryUUID;
    private string $fullName;
    private ?string $shortName = null;
    private NIP $nip;
    private REGON $regon;
    private ?string $description = null;
    private bool $active = true;
    private Address $address;
    private Phones $phones;
    private ?Emails $emails = null;
    private ?Websites $websites = null;

    public static function create(
        string $fullName,
        NIP $nip,
        REGON $regon,
        IndustryUUID $industryUUID,
        bool $active,
        Address $address,
        Phones $phones,
        ?string $shortName = null,
        ?string $description = null,
        ?CompanyUUID $parentCompanyUUID = null,
        ?Emails $emails = null,
        ?Websites $websites = null,
    ): self {
        $aggregate = new self();

        $aggregate->record(new CompanyCreatedEvent(
            CompanyUUID::generate(),
            $fullName,
            $nip,
            $regon,
            $industryUUID,
            $active,
            $address,
            $phones,
            $shortName,
            $description,
            $parentCompanyUUID,
            $emails,
            $websites,
        ));

        return $aggregate;
    }

    public function update(
        string $fullName,
        NIP $nip,
        REGON $regon,
        IndustryUUID $industryUUID,
        bool $active,
        Address $address,
        Phones $phones,
        ?string $shortName = null,
        ?string $description = null,
        ?CompanyUUID $parentCompanyUUID = null,
        ?Emails $emails = null,
        ?Websites $websites = null,
    ): self {
        $this->record(new CompanyUpdatedEvent(
            $this->uuid,
            $fullName,
            $nip,
            $regon,
            $industryUUID,
            $active,
            $address,
            $phones,
            $shortName,
            $description,
            $parentCompanyUUID,
            $emails,
            $websites,
        ));

        return $this;
    }

    protected function apply(DomainEventInterface $event): void
    {
        if ($event instanceof CompanyCreatedEvent || $event instanceof CompanyUpdatedEvent) {
            $this->uuid = $event->uuid;
            $this->fullName = $event->fullName;
            $this->shortName = $event->shortName;
            $this->description = $event->description;
            $this->nip = $event->nip;
            $this->regon = $event->regon;
            $this->parentCompanyUUID = $event->parentCompanyUUID;
            $this->industryUUID = $event->industryUUID;
            $this->active = $event->active;
            $this->address = $event->address;
            $this->phones = $event->phones;
            $this->emails = $event->emails;
            $this->websites = $event->websites;
        }
    }

    public function getUUID(): CompanyUUID
    {
        return $this->uuid;
    }

    public function getParentCompanyUUID(): ?CompanyUUID
    {
        return $this->parentCompanyUUID;
    }

    public function getNIP(): Nip
    {
        return $this->nip;
    }

    public function getREGON(): Regon
    {
        return $this->regon;
    }
}