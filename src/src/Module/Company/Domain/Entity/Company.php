<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Entity;

use App\Common\Domain\Trait\AttributesEntityTrait;
use App\Common\Domain\Trait\RelationsEntityTrait;
use App\Common\Domain\Trait\TimestampableTrait;
use App\Module\Company\Domain\Enum\ContactTypeEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'company')]
#[ORM\Index(name: 'idx_short_name', columns: ['short_name'])]
#[ORM\Index(name: 'idx_nip', columns: ['nip'])]
#[ORM\Index(name: 'idx_regon', columns: ['regon'])]
#[ORM\Index(name: 'idx_internal_code', columns: ['internal_code'])]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class Company
{
    use TimestampableTrait;
    use AttributesEntityTrait;
    use RelationsEntityTrait;

    public const string COLUMN_UUID = 'uuid';
    public const string COLUMN_COMPANY_UUID = 'companyUuid';
    public const string COLUMN_FULL_NAME = 'fullName';
    public const string COLUMN_SHORT_NAME = 'shortName';
    public const string COLUMN_INTERNAL_CODE = 'internalCode';
    public const string COLUMN_DESCRIPTION = 'description';
    public const string COLUMN_NIP = 'nip';
    public const string COLUMN_REGON = 'regon';
    public const string COLUMN_ACTIVE = 'active';
    public const string COLUMN_CREATED_AT = 'createdAt';
    public const string COLUMN_UPDATED_AT = 'updatedAt';
    public const string COLUMN_DELETED_AT = 'deletedAt';
    public const string RELATION_INDUSTRY = 'industry';
    public const string RELATION_DEPARTMENTS = 'departments';
    public const string RELATION_ADDRESS = 'address';
    public const string RELATION_CONTACTS = 'contacts';
    public const string RELATION_PARENT_COMPANY = 'parentCompany';
    public const string ALIAS = 'company';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidInterface $uuid;

    #[ORM\ManyToOne(targetEntity: Company::class)]
    #[ORM\JoinColumn(name: 'company_uuid', referencedColumnName: 'uuid', nullable: true, onDelete: 'CASCADE')]
    private ?Company $parentCompany = null;

    #[ORM\ManyToOne(targetEntity: Industry::class, inversedBy: 'companies')]
    #[ORM\JoinColumn(name: 'industry_uuid', referencedColumnName: 'uuid', nullable: false, onDelete: 'CASCADE')]
    private Industry $industry;

    #[ORM\OneToMany(targetEntity: Contact::class, mappedBy: 'company', cascade: ['persist', 'remove'])]
    private Collection $contacts;

    #[ORM\OneToOne(targetEntity: Address::class, mappedBy: 'company', cascade: ['persist', 'remove'])]
    private Address $address;

    #[ORM\Column(type: Types::STRING, length: 1000)]
    #[Assert\NotBlank]
    private string $fullName;

    #[ORM\Column(type: Types::STRING, length: 200, nullable: true)]
    private ?string $shortName;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    private ?string $internalCode;

    #[ORM\Column(type: Types::STRING, length: 20, nullable: false)]
    private string $nip;

    #[ORM\Column(type: Types::STRING, length: 20, nullable: false)]
    private string $regon;

    #[ORM\Column(type: Types::STRING, length: 500, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    #[Assert\NotBlank]
    private bool $active;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deletedAt = null;

    #[ORM\OneToMany(targetEntity: Department::class, mappedBy: 'company', cascade: ['persist', 'remove'])]
    private Collection $departments;

    public function __construct()
    {
        $this->departments = new ArrayCollection();
        $this->contacts = new ArrayCollection();
    }

    public function getUUID(): UuidInterface
    {
        return $this->{self::COLUMN_UUID};
    }

    public function setUUID(string $uuid): void
    {
        $this->uuid = Uuid::fromString($uuid);
    }

    public function getParentCompany(): ?Company
    {
        return $this->parentCompany;
    }

    public function setParentCompany(?Company $company): void
    {
        $this->parentCompany = $company;
    }

    public function removeParentCompany(): void
    {
        $this->parentCompany = null;
    }

    public function getIndustry(): Industry
    {
        return $this->industry;
    }

    public function setIndustry(Industry $industry): void
    {
        $this->industry = $industry;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): void
    {
        $this->address = $address;
        $address->setCompany($this);
    }

    public function getContacts(?ContactTypeEnum $type = null): Collection
    {
        if (null === $type) {
            return $this->contacts;
        }

        return $this->contacts->filter(fn (Contact $contact) => $contact->getType() === $type->value);
    }

    public function addContact(Contact $contact): void
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts[] = $contact;
            $contact->setCompany($this);
        }
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

    public function getInternalCode(): ?string
    {
        return $this->{self::COLUMN_INTERNAL_CODE};
    }

    public function setInternalCode(?string $internalCode): void
    {
        $this->{self::COLUMN_INTERNAL_CODE} = $internalCode;
    }

    public function getNip(): string
    {
        return $this->nip;
    }

    public function setNip(string $nip): void
    {
        $this->nip = $nip;
    }

    public function getRegon(): string
    {
        return $this->regon;
    }

    public function setRegon(string $regon): void
    {
        $this->regon = $regon;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getActive(): bool
    {
        return $this->{self::COLUMN_ACTIVE};
    }

    public function setActive(bool $active): void
    {
        $this->{self::COLUMN_ACTIVE} = $active;
    }

    public function getDepartments(): Collection
    {
        return $this->departments;
    }

    public function addDepartment(Department $department): void
    {
        if (!$this->departments->contains($department)) {
            $this->departments->add($department);
            $department->setCompany($this);
        }
    }

    public function removeDepartment(Department $department): void
    {
        if ($this->departments->contains($department)) {
            $this->departments->removeElement($department);
            if ($department->getCompany() === $this) {
                $department->setCompany(null);
            }
        }
    }

    public function toArray(): array
    {
        return [
            self::COLUMN_UUID => $this->getUuid()->toString(),
            self::COLUMN_FULL_NAME => $this->getFullName(),
            self::COLUMN_SHORT_NAME => $this->getShortName(),
            self::COLUMN_DESCRIPTION => $this->getDescription(),
            self::COLUMN_NIP => $this->getNip(),
            self::COLUMN_REGON => $this->getRegon(),
            self::COLUMN_ACTIVE => $this->getActive(),
            'quantityDepartments' => $this->getDepartments()->count(),
            self::RELATION_PARENT_COMPANY => $this->getParentCompany() ? $this->getParentCompany()->toArray() : null,
            self::COLUMN_CREATED_AT => $this->getCreatedAt(),
            self::COLUMN_UPDATED_AT => $this->getUpdatedAt(),
            self::COLUMN_DELETED_AT => $this->getDeletedAt(),
        ];
    }
}
