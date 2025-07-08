<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Aggregate\Company;

use App\Common\Domain\Abstract\AggregateRootAbstract;
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
use App\Module\Company\Domain\Event\Company\CompanyCreatedEvent;
use App\Module\Company\Domain\Event\Company\CompanyDeletedEvent;
use App\Module\Company\Domain\Event\Company\CompanyRestoredEvent;
use App\Module\Company\Domain\Event\Company\CompanyUpdatedEvent;

class CompanyAggregate extends AggregateRootAbstract
{
    private CompanyUUID  $uuid;
    private ?CompanyUUID $parentCompanyUUID = null;
    private IndustryUUID $industryUUID;
    private FullName       $fullName;
    private ?ShortName      $shortName         = null;
    private NIP          $nip;
    private REGON        $regon;
    private ?string      $description       = null;
    private bool         $active            = true;
    private Address      $address;
    private ?Phones      $phones            = null;
    private ?Emails      $emails            = null;
    private ?Websites    $websites          = null;
    private bool         $deleted           = false;

    public static function create(
        FullName     $fullName,
        NIP          $nip,
        REGON        $regon,
        IndustryUUID $industryUUID,
        bool         $active,
        Address      $address,
        Phones       $phones,
        ?ShortName   $shortName = null,
        ?string      $description = null,
        ?CompanyUUID $parentCompanyUUID = null,
        ?Emails      $emails = null,
        ?Websites    $websites = null,
    ): self
    {
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
        FullName     $fullName,
        NIP          $nip,
        REGON        $regon,
        IndustryUUID $industryUUID,
        bool         $active,
        Address      $address,
        Phones       $phones,
        ?ShortName   $shortName = null,
        ?string      $description = null,
        ?CompanyUUID $parentCompanyUUID = null,
        ?Emails      $emails = null,
        ?Websites    $websites = null,
    ): self
    {
        if ($this->deleted) {
            throw new \DomainException('Cannot update a deleted company.');
        }

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

    public function delete(): self
    {
        $this->record(new CompanyDeletedEvent($this->uuid));

        return $this;
    }

    public function restore(): self
    {
        if (!$this->deleted) {
            throw new \DomainException('Company is not deleted.');
        }

        $this->record(new CompanyRestoredEvent($this->uuid));

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

        if ($event instanceof CompanyDeletedEvent) {
            $this->deleted = true;
        }

        if ($event instanceof CompanyRestoredEvent) {
            $this->deleted = false;
        }
    }

    public function getUUID(): CompanyUUID
    {
        return $this->uuid;
    }
}