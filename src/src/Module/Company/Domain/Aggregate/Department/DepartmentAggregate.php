<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Aggregate\Department;

use App\Common\Domain\Abstract\AggregateRootAbstract;
use App\Common\Domain\Interface\DomainEventInterface;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\CompanyUUID;
use App\Module\Company\Domain\Aggregate\Department\ValueObject\DepartmentUUID;
use App\Module\Company\Domain\Aggregate\Department\ValueObject\Name;
use App\Module\Company\Domain\Aggregate\ValueObject\Address;
use App\Module\Company\Domain\Aggregate\ValueObject\Emails;
use App\Module\Company\Domain\Aggregate\ValueObject\Phones;
use App\Module\Company\Domain\Aggregate\ValueObject\Websites;
use App\Module\Company\Domain\Event\Department\DepartmentCreatedEvent;
use App\Module\Company\Domain\Event\Department\DepartmentDeletedEvent;
use App\Module\Company\Domain\Event\Department\DepartmentRestoredEvent;
use App\Module\Company\Domain\Event\Department\DepartmentUpdatedEvent;

class DepartmentAggregate extends AggregateRootAbstract
{
    private DepartmentUUID  $uuid;
    private CompanyUUID     $companyUUID;
    private ?DepartmentUUID $parentDepartmentUUID = null;
    private Name            $name;
    private ?string         $description          = null;
    private ?bool           $active               = true;
    private Address         $address;
    private ?Phones         $phones               = null;
    private ?Emails         $emails               = null;
    private ?Websites       $websites             = null;
    private bool            $deleted              = false;

    public static function create(
        CompanyUUID     $companyUUID,
        Name            $name,
        Address         $address,
        bool            $active = true,
        ?string         $description = null,
        ?Phones         $phones = null,
        ?Emails         $emails = null,
        ?Websites       $websites = null,
        ?DepartmentUUID $parentDepartmentUUID = null
    ): self
    {
        $aggregate = new self();

        $aggregate->record(new DepartmentCreatedEvent(
            DepartmentUUID::generate(),
            $companyUUID,
            $name,
            $address,
            $active,
            $description,
            $phones,
            $emails,
            $websites,
            $parentDepartmentUUID
        ));

        return $aggregate;
    }

    public function update(
        CompanyUUID     $companyUUID,
        Name            $name,
        Address         $address,
        bool            $active,
        ?string         $description = null,
        ?Phones         $phones = null,
        ?Emails         $emails = null,
        ?Websites       $websites = null,
        ?DepartmentUUID $parentDepartmentUUID = null
    ): self
    {
        if ($this->deleted) {
            throw new \DomainException('Cannot update a deleted department.');
        }

        $this->record(new DepartmentUpdatedEvent(
            $this->uuid,
            $companyUUID,
            $name,
            $address,
            $active,
            $description,
            $phones,
            $emails,
            $websites,
            $parentDepartmentUUID
        ));

        return $this;
    }

    public function delete(): self
    {
        $this->record(new DepartmentDeletedEvent($this->uuid));

        return $this;
    }

    public function restore(): self
    {
        if (!$this->deleted) {
            throw new \DomainException('Department is not deleted.');
        }

        $this->record(new DepartmentRestoredEvent($this->uuid));

        return $this;
    }

    protected function apply(DomainEventInterface $event): void
    {
        if ($event instanceof DepartmentCreatedEvent || $event instanceof DepartmentUpdatedEvent) {
            $this->uuid = $event->uuid;
            $this->name = $event->name;
            $this->description = $event->description;
            $this->companyUUID = $event->companyUUID;
            $this->parentDepartmentUUID = $event->parentDepartmentUUID;
            $this->active = $event->active;
            $this->address = $event->address;
            $this->phones = $event->phones;
            $this->emails = $event->emails;
            $this->websites = $event->websites;
        }

        if ($event instanceof DepartmentDeletedEvent) {
            $this->deleted = true;
        }

        if ($event instanceof DepartmentRestoredEvent) {
            $this->deleted = false;
        }
    }

    public function getUUID(): DepartmentUUID
    {
        return $this->uuid;
    }
}