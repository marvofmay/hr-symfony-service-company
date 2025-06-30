<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Aggregate\Company;

use App\Common\Domain\Abstract\AbstractAggregateRoot;
use App\Common\Domain\Interface\DomainEventInterface;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\Address;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\CompanyUUID;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\IndustryUUID;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\NIP;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\REGON;
use App\Module\Company\Domain\Event\Company\CompanyCreatedEvent;

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
    public ?Address $address;

    public static function create(
        string $fullName,
        string $shortName,
        string $description = null,
        string $nip,
        string $regon,
        ?CompanyUUID $parentCompanyUUID = null,
        IndustryUUID $industryUUID,
        bool $active,
        ?Address $address
    ): self {
        $aggregate = new self();
        $aggregate->record(new CompanyCreatedEvent(
            CompanyUUID::generate(),
            $fullName,
            $shortName,
            $description,
            $nip,
            $regon,
            $parentCompanyUUID,
            $industryUUID,
            $active,
            $address
        ));

        return $aggregate;
    }

    protected function apply(DomainEventInterface $event): void
    {
        if ($event instanceof CompanyCreatedEvent) {
            $this->uuid = $event->uuid;
            $this->fullName = $event->fullName;
            $this->shortName = $event->shortName;
            $this->description = $event->description;
            $this->nip = new NIP($event->nip);
            $this->regon = new REGON($event->regon);
            $this->parentCompanyUUID = $event->parentCompanyUUID ?? null;
            $this->industryUUID = $event->industryUUID;
            $this->active = $event->active;
            $this->address = $event->address;
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