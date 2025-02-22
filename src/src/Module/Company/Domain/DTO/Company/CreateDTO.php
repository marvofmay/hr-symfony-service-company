<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Company;

use App\Common\Validator\Constraints\MinMaxLength;
use App\Common\Validator\Constraints\NotBlank;
use App\Module\Company\Structure\Validator\Constraints\Company\ExistingCompanyUUID;
use App\Module\Company\Structure\Validator\Constraints\Company\UniqueCompanyFullName;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    required: ['fullName']
)]
class CreateDTO
{
    #[OA\Property(
        description: 'Nazwa tworzonej firmy',
        type: 'string',
        maxLength: 500,
        minLength: 3,
        example: 'QuantumEdge Technologies',
    )]
    #[NotBlank(message: [
        'text' => 'company.name.required',
        'domain' => 'companies',
    ])]
    #[MinMaxLength(min: 3, max: 500, message: [
        'tooShort' => 'company.name.minimumLength',
        'tooLong' => 'company.name.maximumLength',
        'domain' => 'companies',
    ])]
    #[UniqueCompanyFullName]
    public string $fullName = '';

    #[OA\Property(
        description: 'Opcjonalna skrócona nazwa firmy.',
        type: 'string',
        example: 'QET',
        nullable: true
    )]
    public ?string $shortName = null;

    #[OA\Property(
        description: 'Określa, czy firma jest aktywna. Domyślnie wartość to true.',
        type: 'boolean',
        example: true
    )]
    #[Assert\Type(
        type: 'bool',
    )]
    public bool $active = true;

    #[OA\Property(
        description: 'UUID firmy matki',
        type: 'string',
        example: '1343b681-39ea-4917-ae2f-7a9296690116',
    )]
    #[Assert\Uuid(message: 'company.invalidUUID')]
    #[ExistingCompanyUUID(
        message: ['uuidNotExists' => 'company.uuid.notExists', 'domain' => 'companies']
    )]
    public ?string $parentCompanyUUID = null;

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function getParentCompanyUUID(): ?string
    {
        return $this->parentCompanyUUID;
    }
}
