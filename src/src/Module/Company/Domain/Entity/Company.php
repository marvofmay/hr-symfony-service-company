<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Entity;

use App\Common\Trait\AttributesEntityTrait;
use App\Common\Trait\TimestampableTrait;
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
    use TimestampableTrait;
    use AttributesEntityTrait;

    public const COLUMN_UUID = 'uuid';
    public const COLUMN_COMPANY_UUID = 'companyUuid';
    public const COLUMN_FULL_NAME = 'fullName';
    public const COLUMN_SHORT_NAME = 'shortName';
    public const COLUMN_ACTIVE = 'active';
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

    #[ORM\Column(type: Types::STRING, length: 200, nullable: true)]
    #[Groups('company_info')]
    private ?string $shortName;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    #[Assert\NotBlank]
    #[Groups('position_info')]
    private bool $active;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups('company_info')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups('company_info')]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups('company_info')]
    private ?\DateTimeInterface $deletedAt = null;

    public function getUuid(): UuidInterface
    {
        return $this->{self::COLUMN_UUID};
    }

    public function getParentCompany(): ?Company
    {
        return $this->parentCompany;
    }

    public function setParentCompany(?Company $company): void
    {
        $this->parentCompany = $company;
    }

    public function getFullName(): string
    {
        return $this->{self::COLUMN_FULL_NAME};
    }

    public function setFullName(string $fullName): void
    {
        $this->{self::COLUMN_FULL_NAME} = $fullName;
    }

    public function getShortName(): ?string
    {
        return $this->{self::COLUMN_SHORT_NAME};
    }

    public function setShortName(?string $shortName): void
    {
        $this->{self::COLUMN_SHORT_NAME} = $shortName;
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
