<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Aggregate\Employee;

use App\Common\Domain\Abstract\AggregateRootAbstract;
use App\Common\Domain\Interface\DomainEventInterface;
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
use App\Module\Company\Domain\Aggregate\ValueObject\Address;
use App\Module\Company\Domain\Aggregate\ValueObject\Emails;
use App\Module\Company\Domain\Aggregate\ValueObject\Phones;
use App\Module\Company\Domain\Event\Employee\EmployeeCreatedEvent;
use App\Module\Company\Domain\Event\Employee\EmployeeDeletedEvent;
use App\Module\Company\Domain\Event\Employee\EmployeeRestoredEvent;
use App\Module\Company\Domain\Event\Employee\EmployeeUpdatedEvent;
use App\Module\System\Domain\ValueObject\UserUUID;

class EmployeeAggregate extends AggregateRootAbstract
{
    private EmployeeUUID   $uuid;
    private ?EmployeeUUID  $parentEmployeeUUID = null;
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
    private ?string          $internalCode = null;
    private ?EmploymentTo    $employmentTo = null;
    private bool             $active       = true;
    private ?Phones          $phones       = null;
    private bool             $deleted      = false;
    private UserUUID         $loggedUserUUID;

    public static function create(
        FirstName $firstName,
        LastName $lastName,
        PESEL $pesel,
        EmploymentFrom $employmentFrom,
        DepartmentUUID $departmentUUID,
        PositionUUID $positionUUID,
        ContractTypeUUID $contractTypeUUID,
        RoleUUID $roleUUID,
        Emails $emails,
        Address $address,
        UserUUID $loggedUserUUID,
        ?string $externalUUID = null,
        ?string $internalCode = null,
        ?bool $active = true,
        ?Phones $phones = null,
        ?EmployeeUUID $parentEmployeeUUID = null,
        ?EmploymentTo $employmentTo = null,
        ?EmployeeUUID $uuid = null,
    ): self {
        $aggregate = new self();

        $aggregate->record(
            new EmployeeCreatedEvent(
                $uuid ?? EmployeeUUID::generate(),
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
                $loggedUserUUID,
                $active,
                $externalUUID,
                $internalCode,
                $phones,
                $parentEmployeeUUID,
                $employmentTo,
            )
        );

        return $aggregate;
    }

    public function update(
        FirstName $firstName,
        LastName $lastName,
        PESEL $pesel,
        EmploymentFrom $employmentFrom,
        DepartmentUUID $departmentUUID,
        PositionUUID $positionUUID,
        ContractTypeUUID $contractTypeUUID,
        RoleUUID $roleUUID,
        Emails $emails,
        Address $address,
        UserUUID $loggedUserUUID,
        ?string $externalUUID = null,
        ?string $internalCode = null,
        ?bool $active = true,
        ?Phones $phones = null,
        ?EmployeeUUID $parentEmployeeUUID = null,
        ?EmploymentTo $employmentTo = null,
    ): self {
        if ($this->deleted) {
            throw new \DomainException('Cannot update a deleted employee.');
        }

        $this->record(
            new EmployeeUpdatedEvent(
                $this->uuid,
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
                $loggedUserUUID,
                $active,
                $externalUUID,
                $internalCode,
                $phones,
                $parentEmployeeUUID,
                $employmentTo,
            )
        );

        return $this;
    }

    public function delete(): self
    {
        $this->record(new EmployeeDeletedEvent($this->uuid));

        return $this;
    }

    public function restore(): self
    {
        if (!$this->deleted) {
            throw new \DomainException('Employee is not deleted.');
        }

        $this->record(new EmployeeRestoredEvent($this->uuid));

        return $this;
    }

    protected function apply(DomainEventInterface $event): void
    {
        if ($event instanceof EmployeeCreatedEvent || $event instanceof EmployeeUpdatedEvent) {
            $this->uuid = $event->uuid;
            $this->firstName = $event->firstName;
            $this->lastName = $event->lastName;
            $this->pesel = $event->pesel;
            $this->employmentFrom = $event->employmentFrom;
            $this->departmentUUID = $event->departmentUUID;
            $this->positionUUID = $event->positionUUID;
            $this->contractTypeUUID = $event->contractTypeUUID;
            $this->roleUUID = $event->roleUUID;
            $this->emails = $event->emails;
            $this->address = $event->address;
            $this->loggedUserUUID = $event->loggedUserUUID;
            $this->active = $event->active;
            $this->externalUUID = $event->externalUUID;
            $this->internalCode = $event->internalCode;
            $this->phones = $event->phones;
            $this->parentEmployeeUUID = $event->parentEmployeeUUID;
            $this->employmentTo = $event->employmentTo;
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
