<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Entity;

use App\Common\Domain\Trait\AttributesEntityTrait;
use App\Common\Domain\Trait\RelationsEntityTrait;
use App\Common\Domain\Trait\TimestampableTrait;
use App\Module\Company\Domain\Enum\ContactTypeEnum;
use App\Module\Note\Domain\Entity\Note;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Attribute\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'employee')]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class Employee
{
    use TimestampableTrait;
    use AttributesEntityTrait;
    use RelationsEntityTrait;

    public const COLUMN_UUID = 'uuid';
    public const COLUMN_EXTERNAL_UUID = 'externalUUID';
    public const COLUMN_COMPANY_UUID = 'companyUUID';
    public const COLUMN_DEPARTMENT_UUID = 'departmentUUID';
    public const COLUMN_SUPERIOR_UUID = 'superiorUUID';
    public const COLUMN_POSITION_UUID = 'positionUUID';
    public const COLUMN_CONTRACT_TYPE_UUID = 'contractTypeUUID';
    public const COLUMN_ROLE_UUID = 'roleUUID';
    public const COLUMN_FIRST_NAME = 'firstName';
    public const COLUMN_LAST_NAME = 'lastName';
    public const COLUMN_PESEL = 'pesel';
    public const COLUMN_EMPLOYMENT_FROM = 'employmentFrom';
    public const COLUMN_EMPLOYMENT_TO = 'employmentTo';
    public const COLUMN_ACTIVE = 'active';
    public const COLUMN_CREATED_AT = 'createdAt';
    public const COLUMN_UPDATED_AT = 'updatedAt';
    public const COLUMN_DELETED_AT = 'deletedAt';
    public const RELATION_ROLE = 'role';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups('employee_info')]
    private UuidInterface $uuid;

    #[ORM\Column(type: Types::STRING, length: 100, nullable: true)]
    #[Groups('employee_info')]
    private ?string $externalUUID = null;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'employees')]
    #[ORM\JoinColumn(name: 'company_uuid', referencedColumnName: 'uuid', nullable: false, onDelete: 'CASCADE')]
    private ?Company $company;

    #[ORM\ManyToOne(targetEntity: Department::class)]
    #[ORM\JoinColumn(name: 'department_uuid', referencedColumnName: 'uuid', nullable: false, onDelete: 'CASCADE')]
    private ?Department $department;

    #[ORM\ManyToOne(targetEntity: Employee::class)]
    #[ORM\JoinColumn(name: 'employee_uuid', referencedColumnName: 'uuid', nullable: true, onDelete: 'CASCADE')]
    #[Groups('employee_info')]
    private ?Employee $parentEmployee = null;

    #[ORM\ManyToOne(targetEntity: Position::class)]
    #[ORM\JoinColumn(name: 'position_uuid', referencedColumnName: 'uuid', nullable: false, onDelete: 'CASCADE')]
    #[Groups('employee_info')]
    private Position $position;

    #[ORM\ManyToOne(targetEntity: ContractType::class)]
    #[ORM\JoinColumn(name: 'contract_type_uuid', referencedColumnName: 'uuid', nullable: false, onDelete: 'CASCADE')]
    #[Groups('employee_info')]
    private ContractType $contractType;

    #[ORM\ManyToOne(targetEntity: Role::class)]
    #[ORM\JoinColumn(name: 'role_uuid', referencedColumnName: 'uuid', nullable: false, onDelete: 'CASCADE')]
    #[Groups('employee_info')]
    private Role $role;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: false)]
    #[Assert\NotBlank()]
    #[Groups('employee_info')]
    private string $firstName;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: false)]
    #[Assert\NotBlank()]
    #[Groups('employee_info')]
    private string $lastName;

    #[ORM\Column(type: Types::STRING, length: 11, nullable: false)]
    #[Assert\NotBlank()]
    #[Groups('employee_info')]
    private string $pesel;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: false)]
    #[Groups('employee_info')]
    private ?\DateTimeInterface $employmentFrom;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups('employee_info')]
    private ?\DateTimeInterface $employmentTo = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Groups('employee_info')]
    private bool $active = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups('employee_info')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups('employee_info')]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups('employee_info')]
    private ?\DateTimeInterface $deletedAt = null;

    #[ORM\OneToOne(targetEntity: User::class, mappedBy: 'employee', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    #[ORM\OneToMany(targetEntity: Note::class, mappedBy: 'employee', cascade: ['persist', 'remove'])]
    private Collection $notes;

    #[ORM\OneToMany(targetEntity: Contact::class, mappedBy: 'employee', cascade: ['persist', 'remove'])]
    #[Groups('employee_info')]
    private Collection $contacts;

    #[ORM\OneToOne(targetEntity: Address::class, mappedBy: 'employee', cascade: ['persist', 'remove'])]
    #[Groups('employee_info')]
    private ?Address $address = null;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
        $this->contacts = new ArrayCollection();
    }

    public function getUuid(): UuidInterface
    {
        return $this->{self::COLUMN_UUID};
    }

    public function setUuid(UuidInterface $uuid): void
    {
        $this->{self::COLUMN_UUID} = $uuid;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
        if ($user && $user->getEmployee() !== $this) {
            $user->setEmployee($this);
        }
    }

    public function getCompany(): Company
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

    public function getPosition(): Position
    {
        return $this->position;
    }

    public function setPosition(Position $position): void
    {
        $this->position = $position;
    }

    public function getContractType(): ContractType
    {
        return $this->contractType;
    }

    public function setContractType(ContractType $contractType): void
    {
        $this->contractType = $contractType;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function setRole(Role $role): void
    {
        $this->role = $role;
    }

    public function getParentEmployee(): ?Employee
    {
        return $this->parentEmployee;
    }

    public function setParentEmployee(?Employee $parentEmployee): void
    {
        $this->parentEmployee = $parentEmployee;
    }

    public function getExternalUUID(): ?string
    {
        return $this->{self::COLUMN_EXTERNAL_UUID};
    }

    public function setExternalUUID(?string $externalUUID): void
    {
        $this->{self::COLUMN_EXTERNAL_UUID} = $externalUUID;
    }

    public function getFirstName(): string
    {
        return $this->{self::COLUMN_FIRST_NAME};
    }

    public function setFirstName(string $firstName): void
    {
        $this->{self::COLUMN_FIRST_NAME} = $firstName;
    }

    public function getLastName(): string
    {
        return $this->{self::COLUMN_LAST_NAME};
    }

    public function setLastName(string $lastName): void
    {
        $this->{self::COLUMN_LAST_NAME} = $lastName;
    }

    public function getPESEL(): string
    {
        return $this->{self::COLUMN_PESEL};
    }

    public function setPESEL(string $pesel): void
    {
        $this->{self::COLUMN_PESEL} = $pesel;
    }

    public function getEmploymentFrom(): \DateTimeInterface
    {
        return $this->{self::COLUMN_EMPLOYMENT_FROM};
    }

    public function setEmploymentFrom(\DateTimeInterface $employmentFrom): void
    {
        $this->{self::COLUMN_EMPLOYMENT_FROM} = $employmentFrom;
    }

    public function getEmploymentTo(): ?\DateTimeInterface
    {
        return $this->{self::COLUMN_EMPLOYMENT_TO};
    }

    public function setEmploymentTo(?\DateTimeInterface $employmentTo): void
    {
        $this->{self::COLUMN_EMPLOYMENT_TO} = $employmentTo;
    }

    public function getActive(): bool
    {
        return $this->{self::COLUMN_ACTIVE};
    }

    public function setActive(bool $active): void
    {
        $this->{self::COLUMN_ACTIVE} = $active;
    }

    public function getContacts(?ContactTypeEnum $type = null): Collection
    {
        if ($type === null) {
            return $this->contacts;
        }

        return $this->contacts->filter(fn(Contact $contact) => $contact->getType() === $type->value);
    }

    public function addContact(Contact $contact): void
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts[] = $contact;
            $contact->setEmployee($this);
        }
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): void
    {
        $this->address = $address;
        $address->setEmployee($this);
    }

    public function toArray(): array
    {
        return [
            self::COLUMN_FIRST_NAME => $this->firstName,
            self::COLUMN_LAST_NAME => $this->lastName,
            self::COLUMN_CREATED_AT => $this->getCreatedAt(),
            self::COLUMN_UPDATED_AT => $this->getUpdatedAt(),
            self::COLUMN_DELETED_AT => $this->getDeletedAt(),
            self::RELATION_ROLE => $this->getRole()->toArray(),
        ];
    }
}
