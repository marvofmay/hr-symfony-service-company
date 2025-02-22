<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'department')]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class Department
{
    public const COLUMN_UUID = 'uuid';
    public const COLUMN_COMPANY_UUID = 'company_uuid';
    public const COLUMN_DEPARTMENT_UUID = 'department_uuid';
    public const COLUMN_NAME = 'name';
    public const COLUMN_ACTIVE = 'active';

    public const COLUMN_CREATED_AT = 'createdAt';
    public const COLUMN_UPDATED_AT = 'updatedAt';
    public const COLUMN_DELETED_AT = 'deletedAt';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups('department_info')]
    private UuidInterface $uuid;

    #[ORM\ManyToOne(targetEntity: Company::class)]
    #[ORM\JoinColumn(name: 'company_uuid', referencedColumnName: 'uuid', onDelete: 'CASCADE')]
    #[Groups('department_info')]
    private ?Company $company = null;

    #[ORM\ManyToOne(targetEntity: Department::class)]
    #[ORM\JoinColumn(name: 'department_uuid', referencedColumnName: 'uuid', nullable: true, onDelete: 'CASCADE')]
    #[Groups('department_info')]
    private ?Department $parentDepartment = null;

    #[ORM\Column(type: Types::STRING, length: 1000)]
    #[Assert\NotBlank]
    #[Groups('department_info')]
    private string $name;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    #[Assert\NotBlank]
    #[Groups('department_info')]
    private bool $active;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups('department_info')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups('department_info')]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups('department_info')]
    private ?\DateTimeInterface $deletedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): void
    {
        $this->company = $company;
    }

    public function getParentDepartment(): ?Department
    {
        return $this->parentDepartment;
    }

    public function setParentDepartment(?Department $parentDepartment): void
    {
        $this->parentDepartment = $parentDepartment;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->{self::COLUMN_CREATED_AT} = new \DateTime();
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->{self::COLUMN_CREATED_AT};
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->{self::COLUMN_UPDATED_AT};
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): void
    {
        $this->{self::COLUMN_UPDATED_AT} = $updatedAt;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->{self::COLUMN_DELETED_AT};
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): void
    {
        $this->{self::COLUMN_DELETED_AT} = $deletedAt;
    }

    public static function getAttributes(): array
    {
        $reflectionClass = new \ReflectionClass(static::class);
        $properties = $reflectionClass->getProperties(\ReflectionProperty::IS_PRIVATE);

        $attributes = [];
        foreach ($properties as $property) {
            $attributes[] = $property->getName();
        }

        return $attributes;
    }
}
