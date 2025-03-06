<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Entity;

use App\Common\Domain\Trait\AttributesEntityTrait;
use App\Common\Domain\Trait\TimestampableTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'address')]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class Address
{
    use TimestampableTrait;
    use AttributesEntityTrait;

    public const COLUMN_UUID = 'uuid';
    public const COLUMN_COMPANY_UUID = 'companyUUID';
    public const COLUMN_DEPARTMENT_UUID = 'departmentUUID';
    public const COLUMN_EMPLOYEE_UUID = 'employeeUUID';
    public const COLUMN_STREET = 'street';

    public const COLUMN_POSTCODE = 'postcode';
    public const COLUMN_CITY = 'city';
    public const COLUMN_COUNTRY = 'country';
    public const COLUMN_ACTIVE = 'active';
    public const COLUMN_CREATED_AT = 'createdAt';
    public const COLUMN_UPDATED_AT = 'updatedAt';
    public const COLUMN_DELETED_AT = 'deletedAt';
    public const SOFT_DELETED_AT = 'soft';
    public const HARD_DELETED_AT = 'hard';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups('address_info')]
    private UuidInterface $uuid;

    #[ORM\OneToOne(targetEntity: Company::class, inversedBy: 'address')]
    #[ORM\JoinColumn(name: 'company_uuid', referencedColumnName: 'uuid', nullable: true, onDelete: 'CASCADE')]
    private ?Company $company;

    #[ORM\OneToOne(targetEntity: Department::class, inversedBy: 'address')]
    #[ORM\JoinColumn(name: 'department_uuid', referencedColumnName: 'uuid', nullable: true, onDelete: 'CASCADE')]
    private ?Department $department;

    #[ORM\OneToOne(targetEntity: Employee::class, inversedBy: 'address')]
    #[ORM\JoinColumn(name: 'employee_uuid', referencedColumnName: 'uuid', nullable: true, onDelete: 'CASCADE')]
    private ?Employee $employee;

    #[ORM\Column(type: Types::STRING, length: 250, nullable: false)]
    #[Assert\NotBlank()]
    #[Groups('address_info')]
    private string $street;

    #[ORM\Column(type: Types::STRING, length: 10, nullable: false)]
    #[Assert\NotBlank()]
    #[Groups('address_info')]
    private string $postcode;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: false)]
    #[Assert\NotBlank()]
    #[Groups('address_info')]
    private string $city;

    #[ORM\Column(type: Types::STRING, nullable: false)]
    #[Groups('address_info')]
    private string $country;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Groups('address_info')]
    private bool $active = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups('address_info')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups('address_info')]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups('address_info')]
    private ?\DateTimeInterface $deletedAt = null;

    public function getUuid(): UuidInterface
    {
        return $this->{self::COLUMN_UUID};
    }

    public function setUuid(UuidInterface $uuid): void
    {
        $this->{self::COLUMN_UUID} = $uuid;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): void
    {
        $this->company = $company;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(?Department $department): void
    {
        $this->department = $department;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): void
    {
        $this->employee = $employee;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function setStreet(string $street): void
    {
        $this->street = $street;
    }

    public function getPostcode(): string
    {
        return $this->postcode;
    }

    public function setPostcode(string $postcode): void
    {
        $this->postcode = $postcode;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    public function getActive(): bool
    {
        return $this->{self::COLUMN_ACTIVE};
    }

    public function setActive(bool $active): void
    {
        $this->{self::COLUMN_ACTIVE} = $active;
    }
}
