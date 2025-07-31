<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Event\Employee;

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

final readonly class EmployeeCreatedEvent implements DomainEventInterface
{
    public function __construct(
        public EmployeeUUID     $uuid,
        public FirstName        $firstName,
        public LastName         $lastName,
        public PESEL            $pesel,
        public EmploymentFrom   $employmentFrom,
        public DepartmentUUID   $departmentUUID,
        public PositionUUID     $positionUUID,
        public ContractTypeUUID $contractTypeUUID,
        public RoleUUID         $roleUUID,
        public Emails           $emails,
        public Address          $address,
        public bool             $active,
        public ?string          $externalUUID = null,
        public ?Phones          $phones = null,
        public ?EmployeeUUID    $parentEmployeeUUID = null,
        public ?EmploymentTo    $employmentTo = null,
    )
    {
    }
}