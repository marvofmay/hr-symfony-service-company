<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Entity;

use App\Common\Trait\AttributesEntityTrait;
use App\Common\Trait\TimestampableTrait;
use App\Module\Note\Domain\Entity\Note;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: 'employee')]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class Employee
{
    use TimestampableTrait;
    use AttributesEntityTrait;

    public const COLUMN_UUID = 'uuid';
    public const COLUMN_EXTERNAL_UUID = 'external_uuid';
    public const COLUMN_COMPANY_UUID = 'company_uuid';
    public const COLUMN_DEPARTMENT_UUID = 'department_uuid';
    public const COLUMN_SUPERIOR_UUID = 'superior_uuid';
    public const COLUMN_POSITION_UUID = 'position_uuid';
    public const COLUMN_CONTRACT_TYPE_UUID = 'contract_type_uuid';
    public const COLUMN_ROLE_UUID = 'role_uuid';
    public const COLUMN_FIRST_NAME = 'first_name';
    public const COLUMN_LAST_NAME = 'last_name';
    public const COLUMN_PESEL = 'pesel';
    public const COLUMN_EMPLOYMENT_FROM = 'employment_form';
    public const COLUMN_EMPLOYMENT_TO = 'employment_to';
    public const COLUMN_ACTIVE = 'active';
    public const COLUMN_CREATED_AT = 'createdAt';
    public const COLUMN_UPDATED_AT = 'updatedAt';
    public const COLUMN_DELETED_AT = 'deletedAt';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups('employee_info')]
    private UuidInterface $uuid;

    #[ORM\Column(type: Types::STRING, length: 100, nullable: true)]
    #[Groups('employee_info')]
    private ?string $external_uuid = null;

    #[ORM\Column(type: 'uuid')]
    #[Assert\NotBlank()]
    #[Groups('employee_info')]
    private UuidInterface $company_uuid;

    #[ORM\Column(type: 'uuid')]
    #[Assert\NotBlank()]
    #[Groups('employee_info')]
    private UuidInterface $department_uuid;

    #[ORM\Column(type: 'uuid')]
    #[Assert\NotBlank()]
    #[Groups('employee_info')]
    private UuidInterface $superior_uuid;

    #[ORM\Column(type: 'uuid')]
    #[Assert\NotBlank()]
    #[Groups('employee_info')]
    private UuidInterface $position_uuid;

    #[ORM\Column(type: 'uuid')]
    #[Assert\NotBlank()]
    #[Groups('employee_info')]
    private UuidInterface $contract_type_uuid;

    #[ORM\Column(type: 'uuid')]
    #[Assert\NotBlank()]
    #[Groups('employee_info')]
    private UuidInterface $role_uuid;

    #[ORM\Column(type: Types::STRING, length: 50)]
    #[Assert\NotBlank()]
    #[Groups('employee_info')]
    private string $firstName;

    #[ORM\Column(type: Types::STRING, length: 50)]
    #[Assert\NotBlank()]
    #[Groups('employee_info')]
    private string $lastName;

    #[ORM\Column(type: Types::STRING, length: 11)]
    #[Assert\NotBlank()]
    #[Groups('employee_info')]
    private string $pesel;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups('employee_info')]
    private ?\DateTimeInterface $employeeFrom;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups('employee_info')]
    private ?\DateTimeInterface $employeeTo = null;

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

    #[ORM\OneToOne(targetEntity: User::class, mappedBy: 'employee')]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'employee', targetEntity: Note::class, cascade: ['persist', 'remove'])]
    private Collection $notes;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
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

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
