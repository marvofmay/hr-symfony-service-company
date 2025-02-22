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
#[ORM\Table(name: 'company')]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class Company
{
    public const COLUMN_UUID = 'uuid';
    public const COLUMN_COMPANY_UUID = 'company_uuid';
    public const COLUMN_FULL_NAME = 'full_name';
    public const COLUMN_SHORT_NAME = 'short_name';

    public const COLUMN_CREATED_AT = 'createdAt';
    public const COLUMN_UPDATED_AT = 'updatedAt';
    public const COLUMN_DELETED_AT = 'deletedAt';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups('company_info')]
    private UuidInterface $uuid;

    #[ORM\ManyToOne(targetEntity: Company::class)]
    #[ORM\JoinColumn(name: 'company_uuid', referencedColumnName: 'uuid', nullable: true, onDelete: 'CASCADE')]
    #[Groups('company_info')]
    private ?Company $parentCompany = null;

    #[ORM\Column(type: Types::STRING, length: 1000)]
    #[Assert\NotBlank]
    #[Groups('company_info')]
    private string $fullName;

    #[ORM\Column(type: Types::STRING, length: 200)]
    #[Assert\NotBlank]
    #[Groups('company_info')]
    private string $shortName;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups('company_info')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups('company_info')]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups('company_info')]
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
        return $this->parentCompany;
    }

    public function setCompany(?Company $company): void
    {
        $this->parentCompany = $company;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): void
    {
        $this->fullName = $fullName;
    }

    public function getShortName(): string
    {
        return $this->shortName;
    }

    public function setShortName(string $shortName): void
    {
        $this->shortName = $shortName;
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
