<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Department;

use App\Common\Validator\Constraints\MinMaxLength;
use App\Common\Validator\Constraints\NotBlank;
use App\Module\Company\Structure\Validator\Constraints\Company\ExistingCompanyUUID;
use App\Module\Company\Structure\Validator\Constraints\Department\ExistingDepartmentUUID;
use App\Module\Company\Structure\Validator\Constraints\Department\UniqueDepartmentName;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    required: ['name', 'companyUUID'],
)]
class CreateDTO
{
    #[OA\Property(
        description: 'Nazwa tworzonego departamentu',
        type: 'string',
        maxLength: 200,
        minLength: 3,
        example: 'Some department of QuantumEdge Technologies',
    )]
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

    #[OA\Property(
        description: 'Określa, czy departament jest aktywny. Domyślnie wartość to true.',
        type: 'boolean',
        example: true
    )]
    #[Assert\Type(
        type: 'bool',
    )]
    public bool $active = true;

    #[OA\Property(
        description: 'UUID firmy, do której przynależy departament',
        type: 'string',
        example: '1343b681-39ea-4917-ae2f-7a9296690116',
    )]
    #[NotBlank(message: [
        'text' => 'company.uuid.required',
        'domain' => 'companies',
    ])]
    #[Assert\Uuid(message: 'company.invalidUUID')]
    #[ExistingCompanyUUID(
        message: ['uuidNotExists' => 'company.uuid.notExists', 'domain' => 'companies']
    )]
    public ?string $companyUUID = null;

    #[OA\Property(
        description: 'UUID departamentu matki',
        type: 'string',
        example: '1343b681-39ea-4917-ae2f-7a9296690116',
        nullable: true
    )]
    #[Assert\Uuid(message: 'department.invalidUUID')]
    #[ExistingDepartmentUUID(
        message: ['uuidNotExists' => 'department.uuid.notExists', 'domain' => 'departments']
    )]
    public ?string $parentDepartmentUUID = null;

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
}
