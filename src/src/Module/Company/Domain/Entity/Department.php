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
    use TimestampableTrait;
    use AttributesEntityTrait;
    use RelationsEntityTrait;

    public const COLUMN_UUID = 'uuid';
    public const COLUMN_COMPANY_UUID = 'company_uuid';
    public const COLUMN_DEPARTMENT_UUID = 'department_uuid';
    public const COLUMN_NAME = 'name';
    public CONST COLUMN_DESCRIPTION = 'description';
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

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'departments')]
    #[ORM\JoinColumn(name: 'company_uuid', referencedColumnName: 'uuid', nullable: false, onDelete: 'CASCADE')]
    #[Groups('department_info')]
    private ?Company $company;

    #[ORM\ManyToOne(targetEntity: Department::class)]
    #[ORM\JoinColumn(name: 'department_uuid', referencedColumnName: 'uuid', nullable: true, onDelete: 'CASCADE')]
    #[Groups('department_info')]
    private ?Department $parentDepartment = null;

    #[ORM\Column(type: Types::STRING, length: 1000)]
    #[Assert\NotBlank]
    #[Groups('department_info')]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 500, nullable: true)]
    #[Groups('department_info')]
    private ?string $description = null;

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

    #[ORM\OneToMany(targetEntity: Contact::class, mappedBy: 'department', cascade: ['persist', 'remove'])]
    #[Groups('department_info')]
    private Collection $contacts;

    #[ORM\OneToOne(targetEntity: Address::class, mappedBy: 'department', cascade: ['persist', 'remove'])]
    #[Groups('department_info')]
    private Address $address;

    #[ORM\ManyToMany(targetEntity: Position::class, mappedBy: 'departments')]
    #[Groups('department_info')]
    private Collection $positions;

    #[ORM\OneToMany(targetEntity: Employee::class, mappedBy: 'department', cascade: ['persist', 'remove'])]
    #[Groups('department_info')]
    private Collection $employees;

    public function __construct()
    {
        $this->employees = new ArrayCollection();
        $this->positions = new ArrayCollection();
        $this->contacts = new ArrayCollection();
    }

    public function getUuid(): UuidInterface
    {
        return $this->{self::COLUMN_UUID};
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

    public function getPositions(): Collection
    {
        return $this->positions;
    }

    public function addPosition(Position $position): void
    {
        if (!$this->positions->contains($position)) {
            $this->positions->add($position);
            $position->addDepartment($this);
        }
    }

    public function removePosition(Position $position): void
    {
        if ($this->positions->removeElement($position)) {
            $position->removeDepartment($this);
        }
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
        if ($type === null) {
            return $this->contacts;
        }

        return $this->contacts->filter(fn(Contact $contact) => $contact->getType() === $type->value);
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

    public function toArray(): array {
        return [
            self::COLUMN_UUID => $this->getUuid()->toString(),
            self::COLUMN_NAME => $this->getName(),
            self::COLUMN_DESCRIPTION => $this->getDescription(),
            self::COLUMN_ACTIVE => $this->getActive(),
            //ToDo:: use const RELATION_COMPANY
            'company' => $this->getCompany(),
            //ToDo:: use const RELATION_PARENT_DEPARTMENT
            'parentDepartment' => $this->getParentDepartment() ? $this->getParentDepartment()->toArray() : null,
            self::COLUMN_CREATED_AT => $this->getCreatedAt(),
            self::COLUMN_UPDATED_AT => $this->getUpdatedAt(),
            self::COLUMN_DELETED_AT => $this->getDeletedAt(),
            'employees' => $this->getEmployees()->toArray(),
        ];
    }
}
