<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Entity;

use App\Common\Domain\Trait\AttributesEntityTrait;
use App\Common\Domain\Trait\RelationsEntityTrait;
use App\Common\Domain\Trait\TimeStampableTrait;
use App\Module\Company\Domain\Enum\Contact\ContactTypeEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'department')]
#[ORM\Index(name: 'idx_internal_code', columns: ['internal_code'])]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class Department
{
    use TimeStampableTrait;
    use AttributesEntityTrait;
    use RelationsEntityTrait;

    public const string COLUMN_UUID = 'uuid';
    public const string COLUMN_COMPANY_UUID = 'company_uuid';
    public const string COLUMN_DEPARTMENT_UUID = 'department_uuid';
    public const string COLUMN_NAME = 'name';
    public const string COLUMN_INTERNAL_CODE = 'internalCode';
    public const string COLUMN_DESCRIPTION = 'description';
    public const string COLUMN_ACTIVE = 'active';
    public const string COLUMN_CREATED_AT = 'createdAt';
    public const string COLUMN_UPDATED_AT = 'updatedAt';
    public const string COLUMN_DELETED_AT = 'deletedAt';
    public const string RELATION_EMPLOYEES = 'employees';
    public const string RELATION_PARENT_DEPARTMENT = 'parentDepartment';
    public const string RELATION_COMPANY = 'company';
    public const string RELATION_CONTACTS = 'contacts';
    public const string RELATION_ADDRESS = 'address';
    public const string ALIAS = 'department';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidInterface $uuid;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'departments')]
    #[ORM\JoinColumn(name: 'company_uuid', referencedColumnName: 'uuid', onDelete: 'CASCADE')]
    private ?Company $company;

    #[ORM\ManyToOne(targetEntity: Department::class)]
    #[ORM\JoinColumn(name: 'department_uuid', referencedColumnName: 'uuid', nullable: true, onDelete: 'CASCADE')]
    private ?Department $parentDepartment = null;

    #[ORM\Column(type: Types::STRING, length: 1000)]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 50)]
    private ?string $internalCode;

    #[ORM\Column(type: Types::STRING, length: 500, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    #[Assert\NotBlank]
    private bool $active;

    #[ORM\OneToMany(targetEntity: Contact::class, mappedBy: 'department', cascade: ['persist', 'remove'])]
    private Collection $contacts;

    #[ORM\OneToOne(targetEntity: Address::class, mappedBy: 'department', cascade: ['persist', 'remove'])]
    private Address $address;

    #[ORM\OneToMany(targetEntity: Employee::class, mappedBy: 'department', cascade: ['persist', 'remove'])]
    private Collection $employees;

    #[ORM\OneToMany(targetEntity: PositionDepartment::class, mappedBy: 'department', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $positionDepartments;

    public function __construct()
    {
        $this->employees = new ArrayCollection();
        $this->contacts = new ArrayCollection();
        $this->positionDepartments = new ArrayCollection();
    }

    public function getUUID(): UuidInterface
    {
        return $this->uuid;
    }

    public function setUUID(string $uuid): void
    {
        $this->uuid = Uuid::fromString($uuid);
    }

    public function getCompany(): Company
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

    public function removeParentDepartment(): void
    {
        $this->parentDepartment = null;
    }

    public function getName(): string
    {
        return $this->{self::COLUMN_NAME};
    }

    public function setName(string $name): void
    {
        $this->{self::COLUMN_NAME} = $name;
    }

    public function getInternalCode(): ?string
    {
        return $this->{self::COLUMN_INTERNAL_CODE};
    }

    public function setInternalCode(?string $internalCode): void
    {
        $this->{self::COLUMN_INTERNAL_CODE} = $internalCode;
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

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): void
    {
        $this->address = $address;
        $address->setDepartment($this);
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
            $contact->setDepartment($this);
        }
    }

    public function getEmployees(): Collection
    {
        return $this->employees;
    }

    public function addEmployee(Employee $employee): void
    {
        if (!$this->employees->contains($employee)) {
            $this->employees->add($employee);
            $employee->setDepartment($this);
        }
    }

    public function removeEmployee(Employee $employee): void
    {
        if ($this->employees->contains($employee)) {
            $this->employees->removeElement($employee);
            if ($employee->getDepartment() === $this) {
                $employee->setDepartment(null);
            }
        }
    }
}
