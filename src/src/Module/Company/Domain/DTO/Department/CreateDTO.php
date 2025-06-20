<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Department;

use App\Common\Domain\DTO\AddressDTO;
use App\Common\Validator\Constraints\MinMaxLength;
use App\Common\Validator\Constraints\NotBlank;
use App\Module\Company\Structure\Validator\Constraints\Company\ExistingCompanyUUID;
use App\Module\Company\Structure\Validator\Constraints\Department\ExistingDepartmentUUID;
use App\Module\Company\Structure\Validator\Constraints\Department\UniqueDepartmentName;
use Symfony\Component\Validator\Constraints as Assert;

class CreateDTO
{
    #[NotBlank(message: [
        'text' => 'department.name.required',
        'domain' => 'departments',
    ])]
    #[MinMaxLength(min: 3, max: 200, message: [
        'tooShort' => 'department.name.minimumLength',
        'tooLong' => 'department.name.maximumLength',
        'domain' => 'companies',
    ])]
    #[UniqueDepartmentName]
    public string $name = '';

    #[MinMaxLength(min: 3, max: 500, message: [
        'tooShort' => 'department.description.minimumLength',
        'tooLong' => 'department.description.maximumLength',
        'domain' => 'companies',
    ])]
    public ?string $description = null;

    #[Assert\Type(
        type: 'bool',
    )]
    public bool $active = true;

    #[NotBlank(message: [
        'text' => 'company.uuid.required',
        'domain' => 'companies',
    ])]
    #[Assert\Uuid(message: 'company.invalidUUID')]
    #[ExistingCompanyUUID(
        message: ['uuidNotExists' => 'company.uuid.notExists', 'domain' => 'companies']
    )]
    public ?string $companyUUID = null;

    #[Assert\Uuid(message: 'department.invalidUUID')]
    #[ExistingDepartmentUUID(
        message: ['uuidNotExists' => 'department.uuid.notExists', 'domain' => 'departments']
    )]
    public ?string $parentDepartmentUUID = null;

    #[Assert\All([
        new Assert\Type(type: 'string'),
    ])]
    #[Assert\Type('array')]
    #[Assert\Count(
        max: 3,
        maxMessage: 'phones.max'
    )]
    public ?array $phones = [];

    #[Assert\All([
        new Assert\Type(type: 'string'),
    ])]
    #[Assert\Type('array')]
    #[Assert\Count(
        max: 3,
        maxMessage: 'emails.max'
    )]
    public ?array $emails = [];

    #[Assert\All([
        new Assert\Type(type: 'string'),
    ])]
    #[Assert\Type('array')]
    #[Assert\Count(
        max: 3,
        maxMessage: 'websites.max'
    )]
    public ?array $websites = [];

    #[Assert\NotBlank]
    #[Assert\Valid]
    public AddressDTO $address;

    public function getName(): string
    {
        return $this->name;
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function getCompanyUUID(): ?string
    {
        return $this->companyUUID;
    }

    public function getParentDepartmentUUID(): ?string
    {
        return $this->parentDepartmentUUID;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getPhones(): ?array
    {
        return $this->phones;
    }

    public function getEmails(): ?array
    {
        return $this->emails;
    }

    public function getWebsites(): ?array
    {
        return $this->websites;
    }

    public function getAddress(): AddressDTO
    {
        return $this->address;
    }
}
