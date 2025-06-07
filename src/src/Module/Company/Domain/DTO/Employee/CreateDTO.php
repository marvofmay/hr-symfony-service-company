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
    #[ExistingDepartmentUUID(
        message: ['uuidNotExists' => 'department.uuid.notExists', 'domain' => 'departments']
    )]
    public string $departmentUUID;

    #[Assert\Uuid(message: 'position.invalidUUID')]
    #[NotBlank(message: [
        'text' => 'position.uuid.required',
        'domain' => 'positions',
    ])]
    #[ExistingPositionUUID(
        message: ['uuidNotExists' => 'position.uuid.notExists', 'domain' => 'positions']
    )]
    public string $positionUUID;

    #[Assert\Uuid(message: 'contractType.invalidUUID')]
    #[NotBlank(message: [
        'text' => 'contractType.uuid.required',
        'domain' => 'contract_types',
    ])]
    #[ExistingContractTypeUUID(
        message: ['uuidNotExists' => 'contractType.uuid.notExists', 'domain' => 'contract_types']
    )]
    public string $contractTypeUUID;

    #[Assert\Uuid(message: 'role.invalidUUID')]
    #[NotBlank(message: [
        'text' => 'role.uuid.required',
        'domain' => 'roles',
    ])]
    #[ExistingRoleUUID(
        message: ['uuidNotExists' => 'role.uuid.notExists', 'domain' => 'roles']
    )]
    public string $roleUUID;

    #[Assert\Uuid(message: 'department.invalidUUID')]
    #[ExistingEmployeeUUID(
        message: ['uuidNotExists' => 'employee.uuid.notExists', 'domain' => 'employees']
    )]
    public ?string $parentEmployeeUUID = null;

    public ?string $externalUUID;

    #[NotBlank(message: [
        'text' => 'employee.email.required',
        'domain' => 'employees',
    ])]
    #[Assert\Email(message: 'email.invalid')]
    #[UniqueEmployeeEmail]
    public string $email;

    #[NotBlank(message: [
        'text' => 'Employee.firstName.required',
        'domain' => 'employees',
    ])]
    #[MinMaxLength(min: 3, max: 50, message: [
        'tooShort' => 'employee.firstName.minimumLength',
        'tooLong' => 'employee.firstName.maximumLength',
        'domain' => 'employees',
    ])]
    public string $firstName;

    #[NotBlank(message: [
        'text' => 'Employee.lastName.required',
        'domain' => 'employees',
    ])]
    #[MinMaxLength(min: 3, max: 50, message: [
        'tooShort' => 'employee.lastName.minimumLength',
        'tooLong' => 'employee.lastName.maximumLength',
        'domain' => 'employees',
    ])]
    public string $lastName;

    #[Assert\Type(
        type: 'string',
    )]
    #[NotBlank(message: [
        'text' => 'Employee.pesel.required',
        'domain' => 'employees',
    ])]
    public string $pesel;

    #[Assert\Date]
    #[NotBlank(message: [
        'text' => 'employee.employmentFrom.required',
        'domain' => 'employees',
    ])]
    public string $employmentFrom;

    #[Assert\Date]
    public ?string $employmentTo = null;

    #[Assert\Type(
        type: 'bool',
    )]
    public bool $active = true;

    #[Assert\All([
        new Assert\Type(type: 'string')
    ])]
    #[Assert\Type('array')]
    #[Assert\Count(
        min: 1,
        max: 3,
        minMessage: 'phones.min',
        maxMessage: 'phones.max'
    )]
    public ?array $phones = [];

    #[Assert\NotBlank]
    #[Assert\Valid]
    public AddressDTO $address;

    public function getDepartmentUUID(): ?string
    {
        return $this->departmentUUID;
    }

    public function getPositionUUID(): ?string
    {
        return $this->positionUUID;
    }

    public function getContractTypeUUID(): ?string
    {
        return $this->contractTypeUUID;
    }

    public function getRoleUUID(): ?string
    {
        return $this->roleUUID;
    }

    public function getParentEmployeeUUID(): ?string
    {
        return $this->parentEmployeeUUID;
    }

    public function getExternalUUID(): ?string
    {
        return $this->externalUUID;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getPESEL(): ?string
    {
        return $this->pesel;
    }

    public function getEmploymentFrom(): ?string
    {
        return $this->employmentFrom;
    }

    public function getEmploymentTo(): ?string
    {
        return $this->employmentTo;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function getPhones(): ?array
    {
        return $this->phones;
    }

    public function getAddress(): AddressDTO
    {
        return $this->address;
    }
}
