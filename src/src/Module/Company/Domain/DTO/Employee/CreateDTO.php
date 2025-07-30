<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Employee;

use App\Common\Domain\DTO\AddressDTO;
use App\Common\Validator\Constraints\MinMaxLength;
use App\Common\Validator\Constraints\NotBlank;
use App\Module\Company\Structure\Validator\Constraints\ContractType\ExistingContractTypeUUID;
use App\Module\Company\Structure\Validator\Constraints\Department\ExistingDepartmentUUID;
use App\Module\Company\Structure\Validator\Constraints\Employee\ExistingEmployeeUUID;
use App\Module\Company\Structure\Validator\Constraints\Employee\UniqueEmployeeEmail;
use App\Module\Company\Structure\Validator\Constraints\Position\ExistingPositionUUID;
use App\Module\Company\Structure\Validator\Constraints\Role\ExistingRoleUUID;
use Symfony\Component\Validator\Constraints as Assert;

class CreateDTO
{
    #[NotBlank(message: [
        'text' => 'department.uuid.required',
        'domain' => 'departments',
    ])]
    #[Assert\Uuid(message: 'department.invalidUUID')]
    public string $departmentUUID {
        get {
            return $this->departmentUUID;
        }
    }

    #[Assert\Uuid(message: 'position.invalidUUID')]
    #[NotBlank(message: [
        'text' => 'position.uuid.required',
        'domain' => 'positions',
    ])]
    public string $positionUUID {
        get {
            return $this->positionUUID;
        }
    }

    #[Assert\Uuid(message: 'contractType.invalidUUID')]
    #[NotBlank(message: [
        'text' => 'contractType.uuid.required',
        'domain' => 'contract_types',
    ])]
    public string $contractTypeUUID {
        get {
            return $this->contractTypeUUID;
        }
    }

    #[Assert\Uuid(message: 'role.invalidUUID')]
    #[NotBlank(message: [
        'text' => 'role.uuid.required',
        'domain' => 'roles',
    ])]
    public string $roleUUID {
        get {
            return $this->roleUUID;
        }
    }

    #[Assert\Uuid(message: 'employee.invalidUUID')]
    public ?string $parentEmployeeUUID = null {
        get {
            return $this->parentEmployeeUUID;
        }
    }

    public ?string $externalUUID {
        get {
            return $this->externalUUID;
        }
    }

    #[NotBlank(message: [
        'text' => 'employee.email.required',
        'domain' => 'employees',
    ])]
    #[Assert\Email(message: 'email.invalid')]
    public string $email {
        get {
            return $this->email;
        }
    }

    #[NotBlank(message: [
        'text' => 'Employee.firstName.required',
        'domain' => 'employees',
    ])]
    #[MinMaxLength(min: 3, max: 50, message: [
        'tooShort' => 'employee.firstName.minimumLength',
        'tooLong' => 'employee.firstName.maximumLength',
        'domain' => 'employees',
    ])]
    public string $firstName {
        get {
            return $this->firstName;
        }
    }

    #[NotBlank(message: [
        'text' => 'Employee.lastName.required',
        'domain' => 'employees',
    ])]
    #[MinMaxLength(min: 3, max: 50, message: [
        'tooShort' => 'employee.lastName.minimumLength',
        'tooLong' => 'employee.lastName.maximumLength',
        'domain' => 'employees',
    ])]
    public string $lastName {
        get {
            return $this->lastName;
        }
    }

    #[Assert\Type(
        type: 'string',
    )]
    #[NotBlank(message: [
        'text' => 'Employee.pesel.required',
        'domain' => 'employees',
    ])]
    public string $pesel {
        get {
            return $this->pesel;
        }
    }

    #[Assert\Date]
    #[NotBlank(message: [
        'text' => 'employee.employmentFrom.required',
        'domain' => 'employees',
    ])]
    public string $employmentFrom {
        get {
            return $this->employmentFrom;
        }
    }

    #[Assert\Date]
    public ?string $employmentTo = null {
        get {
            return $this->employmentTo;
        }
    }

    #[Assert\Type(
        type: 'bool',
    )]
    public bool $active = true {
        get {
            return $this->active;
        }
    }

    #[Assert\All([
        new Assert\Type(type: 'string'),
    ])]
    #[Assert\Type('array')]
    #[Assert\Count(
        min: 1,
        max: 3,
        minMessage: 'phones.min',
        maxMessage: 'phones.max'
    )]
    public ?array $phones = [] {
        get {
            return $this->phones;
        }
    }

    #[Assert\NotBlank]
    #[Assert\Valid]
    public AddressDTO $address {
        get {
            return $this->address;
        }
    }

}
