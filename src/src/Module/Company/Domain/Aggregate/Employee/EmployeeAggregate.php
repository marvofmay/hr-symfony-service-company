<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Aggregate\Employee;

use App\Common\Domain\Abstract\AggregateRootAbstract;
use App\Common\Domain\Interface\DomainEventInterface;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\Address;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\Emails;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\Phones;
use App\Module\Company\Domain\Aggregate\Department\ValueObject\DepartmentUUID;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\ContractTypeUUID;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\EmployeeUUID;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\EmploymentFrom;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\EmploymentTo;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\FirstName;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\LastName;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\PESEL;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\PositionUUID;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\RoleUUID;
use App\Module\Company\Domain\Event\Employee\EmployeeCreatedEvent;
use App\Module\Company\Domain\Event\Employee\EmployeeDeletedEvent;
use App\Module\Company\Domain\Event\Employee\EmployeeRestoredEvent;
use App\Module\Company\Domain\Event\Employee\EmployeeUpdatedEvent;

class EmployeeAggregate extends AggregateRootAbstract
{
    private FirstName      $firstName;
    private LastName       $lastName;
    private PESEL          $pesel;
    private EmploymentFrom $employmentFrom;

    private DepartmentUUID   $departmentUUID;
    private PositionUUID     $positionUUID;
    private ContractTypeUUID $contractTypeUUID;
    private RoleUUID         $roleUUID;
    private Emails           $emails;
    private Address          $address;
    private ?string          $externalUUID = null;
    private ?EmploymentTo    $employmentTo = null;
    private ?EmployeeUUID    $uuid         = null;
    private bool             $active       = true;
    private ?Phones          $phones       = null;
    private bool             $deleted      = false;

    public static function create(
        FirstName        $firstName,
        LastName         $lastName,
        PESEL            $pesel,
        EmploymentFrom   $employmentFrom,
        DepartmentUUID   $departmentUUID,
        PositionUUID     $positionUUID,
        ContractTypeUUID $contractTypeUUID,
        RoleUUID         $roleUUID,
        Emails           $emails,
        Address          $address,
        ?string          $externalUUID = null,
        bool             $active,
        ?Phones          $phones = null,
        ?EmployeeUUID    $parentEmployeeUUID = null,
        ?EmploymentTo    $employmentTo = null,
    ): self
    {
        $aggregate = new self();

        $aggregate->record(new EmployeeCreatedEvent(
            EmployeeUUID::generate(),
            $firstName,
            $lastName,
            $pesel,
            $employmentFrom,
            $departmentUUID,
            $positionUUID,
            $contractTypeUUID,
            $roleUUID,
            $emails,
            $address,
            $active,
            $externalUUID,
            $phones,
            $parentEmployeeUUID,
            $employmentTo,
        ));

        return $aggregate;
    }

    //public function update(
    //    FullName     $fullName,
    //    NIP          $nip,
    //    REGON        $regon,
    //    IndustryUUID $industryUUID,
    //    bool         $active,
    //    Address      $address,
    //    Phones       $phones,
    //    ?ShortName   $shortName = null,
    //    ?string      $description = null,
    //    ?CompanyUUID $parentCompanyUUID = null,
    //    ?Emails      $emails = null,
    //): self
    //{
    //    if ($this->deleted) {
    //        throw new \DomainException('Cannot update a deleted company.');
    //    }
    //
    //    $this->record(new CompanyUpdatedEvent(
    //        $this->uuid,
    //        $fullName,
    //        $nip,
    //        $regon,
    //        $industryUUID,
    //        $active,
    //        $address,
    //        $phones,
    //        $shortName,
    //        $description,
    //        $parentCompanyUUID,
    //        $emails,
    //    ));
    //
    //    return $this;
    //}
    //
    //public function delete(): self
    //{
    //    $this->record(new CompanyDeletedEvent($this->uuid));
    //
    //    return $this;
    //}
    //
    //public function restore(): self
    //{
    //    if (!$this->deleted) {
    //        throw new \DomainException('Company is not deleted.');
    //    }
    //
    //    $this->record(new CompanyRestoredEvent($this->uuid));
    //
    //    return $this;
    //}

    protected function apply(DomainEventInterface $event): void
    {
        if ($event instanceof EmployeeCreatedEvent || $event instanceof EmployeeUpdatedEvent) {
            $this->uuid = $event->uuid;
        }

        if ($event instanceof EmployeeDeletedEvent) {
            $this->deleted = true;
        }

        if ($event instanceof EmployeeRestoredEvent) {
            $this->deleted = false;
        }
    }

    public function getUUID(): EmployeeUUID
    {
        return $this->uuid;
    }
}