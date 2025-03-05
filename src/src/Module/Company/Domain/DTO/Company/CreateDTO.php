<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Company;

use App\Common\Domain\DTO\AddressDTO;
use App\Common\Validator\Constraints\MinMaxLength;
use App\Common\Validator\Constraints\NotBlank;
use App\Module\Company\Structure\Validator\Constraints\Company\ExistingCompanyUUID;
use App\Module\Company\Structure\Validator\Constraints\Company\UniqueCompanyFullName;
use App\Module\Company\Structure\Validator\Constraints\Industry\ExistingIndustryUUID;
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
        'text' => 'company.fullName.required',
        'domain' => 'companies',
    ])]
    #[MinMaxLength(min: 3, max: 500, message: [
        'tooShort' => 'company.fullName.minimumLength',
        'tooLong' => 'company.fullName.maximumLength',
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
        nullable: true,
    )]
    #[Assert\Uuid(message: 'invalidUUID')]
    #[ExistingCompanyUUID(
        message: ['uuidNotExists' => 'company.uuid.notExists', 'domain' => 'companies']
    )]
    public ?string $parentCompanyUUID = null;

    #[NotBlank(message: [
        'text' => 'company.industryUUID.required',
        'domain' => 'companies',
    ])]
    #[Assert\Uuid(message: 'invalidUUID')]
    #[ExistingIndustryUUID(
        message: ['uuidNotExists' => 'industry.uuid.notExists', 'domain' => 'industries']
    )]
    public ?string $industryUUID = null;

    #[MinMaxLength(min: 10, max: 20, message: [
        'tooShort' => 'company.nip.minimumLength',
        'tooLong' => 'company.nip.maximumLength',
        'domain' => 'companies',
    ])]
    public ?string $nip = null;

    #[MinMaxLength(min: 14, max: 20, message: [
        'tooShort' => 'company.regon.minimumLength',
        'tooLong' => 'company.regon.maximumLength',
        'domain' => 'companies',
    ])]
    public ?string $regon = null;

    #[MinMaxLength(min: 3, max: 500, message: [
        'tooShort' => 'company.description.minimumLength',
        'tooLong' => 'company.description.maximumLength',
        'domain' => 'companies',
    ])]
    public ?string $description = null;

    #[Assert\All([
        new Assert\Type(type: 'string')
    ])]
    #[Assert\Type('array')]
    #[Assert\Count(
        max: 3,
        maxMessage: 'phones.max'
    )]
    public ?array $phones = [];

    #[Assert\All([
        new Assert\Type(type: 'string')
    ])]
    #[Assert\Type('array')]
    #[Assert\Count(
        max: 3,
        maxMessage: 'emails.max'
    )]
    public ?array $emails = [];

    #[Assert\All([
        new Assert\Type(type: 'string')
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

    public function getIndustryUUID(): ?string
    {
        return $this->industryUUID;
    }

    public function getNIP(): ?string
    {
        return $this->nip;
    }

    public function getREGON(): ?string
    {
        return $this->regon;
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
