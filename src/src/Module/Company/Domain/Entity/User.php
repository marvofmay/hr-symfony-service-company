<?php

declare(strict_types = 1);

namespace App\module\company\Domain\Entity;

use App\module\company\Domain\Entity\Employee;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use ReflectionClass;
use ReflectionProperty;
use DateTimeInterface;
use LogicException;

#[ORM\Entity]
#[ORM\Table(
    name: "user",
    uniqueConstraints: [
        new UniqueConstraint(name: "unique_email", columns: ["email"])
    ]
)]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: "deletedAt", timeAware: false, hardDelete: true)]
class User implements PasswordAuthenticatedUserInterface, UserInterface
{
    public const COLUMN_UUID = 'uuid';
    public const COLUMN_EMPLOYEE_UUID = 'employee_uuid';
    public const COLUMN_EMAIL = 'email';
    public const COLUMN_PASSWORD = 'password';
    public const COLUMN_CREATED_AT = 'createdAt';
    public const COLUMN_UPDATED_AT = 'updatedAt';
    public const COLUMN_DELETED_AT = 'deletedAt';
    public const RELATION_ROLES = 'roles';

    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups("user_info")]
    private UuidInterface $uuid;

    #[ORM\Column(type: "uuid", nullable: true)]
    #[Assert\NotBlank()]
    #[Groups("user_info")]
    private ?UuidInterface $employee_uuid = null;

    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    #[Assert\NotBlank()]
    #[Assert\Email()]
    #[Groups("user_info")]
    private string $email;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank()]
    #[Groups("user_info")]
    private string $password;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ["default" => "CURRENT_TIMESTAMP"])]
    #[Groups("user_info")]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups("user_info")]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups("user_info")]
    private ?\DateTimeInterface $deletedAt = null;

    #[ORM\OneToOne(targetEntity: Employee::class)]
    #[ORM\JoinColumn(name: "employee_uuid", referencedColumnName: "uuid", nullable: false)]
    private Employee $employee;

    public function __construct(private readonly UserPasswordHasherInterface $userPasswordHasher)
    {}

    public function hashPassword(string $password): string
    {
        if ($this->userPasswordHasher === null) {
            throw new LogicException('Password hasher is not set.');
        }

        return $this->userPasswordHasher->hashPassword($this, $password);
    }

    public function getUuid(): UuidInterface
    {
        return $this->{self::COLUMN_UUID};
    }

    public function getEmployeeUuid(): ?string
    {
        return $this->{self::COLUMN_EMPLOYEE_UUID};
    }

    public function getEmail(): string
    {
        return $this->{self::COLUMN_EMAIL};
    }

    public function getPassword(): string
    {
        return $this->{self::COLUMN_PASSWORD};
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->{self::COLUMN_CREATED_AT};
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->{self::COLUMN_UPDATED_AT};
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->{self::COLUMN_DELETED_AT};
    }

    public function getRoles(): array
    {
        return [];
    }

    public function getRolesEntities(): Collection
    {
        return $this->{self::RELATION_ROLES};
    }
    public function setUUID(UuidInterface $uuid): void
    {
        $this->{self::COLUMN_UUID} = $uuid;
    }

    public function setEmployeeUuid(?string $employeeUuid): self
    {
        $this->{self::COLUMN_EMPLOYEE_UUID} = $employeeUuid;

        return $this;
    }

    public function setEmail(string $email): void
    {
        $this->{self::COLUMN_EMAIL} = $email;
    }

    public function setPassword(string $password): void
    {
        $this->{self::COLUMN_PASSWORD} = $this->hashPassword($password);
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->{self::COLUMN_CREATED_AT} = $createdAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): void
    {
        $this->{self::COLUMN_UPDATED_AT} = $updatedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): void
    {
        $this->{self::COLUMN_DELETED_AT} = $deletedAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->{self::COLUMN_CREATED_AT} = new \DateTime();
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getEmployee(): Employee
    {
        return $this->employee;
    }

    public function setEmployee(Employee $employee): self
    {
        $this->employee = $employee;
        $this->{self::COLUMN_EMPLOYEE_UUID} = $employee->getUuid();

        return $this;
    }

    public static function getAttributes(): array
    {
        $reflectionClass = new ReflectionClass(static::class);
        $properties = $reflectionClass->getProperties(ReflectionProperty::IS_PRIVATE);

        $attributes = [];
        foreach ($properties as $property) {
            $attributes[] = $property->getName();
        }

        return $attributes;
    }
}
