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
#[ORM\Table(name: 'contact')]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class Contact
{
    use TimestampableTrait;
    use AttributesEntityTrait;

    public const COLUMN_UUID = 'uuid';
    public const COLUMN_COMPANY_UUID = 'companyUUID';
    public const COLUMN_DEPARTMENT_UUID = 'departmentUUID';
    public const COLUMN_EMPLOYEE_UUID = 'employeeUUID';
    public const COLUMN_TYPE = 'type';

    public const COLUMN_DATA = 'data';
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
    #[Groups('contact_info')]
    private UuidInterface $uuid;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'contacts')]
    #[ORM\JoinColumn(name: 'company_uuid', referencedColumnName: 'uuid', nullable: true, onDelete: 'CASCADE')]
    private ?Company $company;

    #[ORM\ManyToOne(targetEntity: Department::class, inversedBy: 'contacts')]
    #[ORM\JoinColumn(name: 'department_uuid', referencedColumnName: 'uuid', nullable: true, onDelete: 'CASCADE')]
    #[Groups('contact_info')]
    private ?Department $department;

    #[ORM\ManyToOne(targetEntity: Employee::class, inversedBy: 'contacts')]
    #[ORM\JoinColumn(name: 'employee_uuid', referencedColumnName: 'uuid', nullable: true, onDelete: 'CASCADE')]
    #[Groups('contact_info')]
    private ?Employee $employee;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: false)]
    #[Assert\NotBlank()]
    #[Groups('contact_info')]
    private string $type;

    #[ORM\Column(type: Types::STRING, length: 100, nullable: false)]
    #[Assert\NotBlank()]
    #[Groups('contact_info')]
    private string $data;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    #[Groups('contact_info')]
    private bool $active = true;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups('contact_info')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups('contact_info')]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups('contact_info')]
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

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function setData(string $data): void
    {
        $this->data = $data;
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
