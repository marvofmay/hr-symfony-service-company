<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Entity;

use App\Common\Domain\Trait\AttributesEntityTrait;
use App\Common\Domain\Trait\TimestampableTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(
    name: 'user',
    uniqueConstraints: [
        new UniqueConstraint(name: 'unique_email', columns: ['email']),
    ]
)]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class User implements PasswordAuthenticatedUserInterface, UserInterface
{
    use TimestampableTrait;
    use AttributesEntityTrait;

    public const COLUMN_UUID = 'uuid';
    public const COLUMN_EMPLOYEE_UUID = 'employee_uuid';
    public const COLUMN_EMAIL = 'email';
    public const COLUMN_PASSWORD = 'password';
    public const COLUMN_CREATED_AT = 'createdAt';
    public const COLUMN_UPDATED_AT = 'updatedAt';
    public const COLUMN_DELETED_AT = 'deletedAt';
    public const RELATION_ROLES = 'roles';

    public const SOFT_DELETED_AT = 'soft';
    public const HARD_DELETED_AT = 'hard';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups('user_info')]
    private UuidInterface $uuid;

    #[ORM\Column(type: 'uuid', nullable: true)]
    #[Assert\NotBlank()]
    #[Groups('user_info')]
    private ?UuidInterface $employee_uuid = null;

    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    #[Assert\NotBlank()]
    #[Assert\Email()]
    #[Groups('user_info')]
    private string $email;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank()]
    #[Groups('user_info')]
    private string $password;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups('user_info')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups('user_info')]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups('user_info')]
    private ?\DateTimeInterface $deletedAt = null;

    #[ORM\OneToOne(targetEntity: Employee::class, inversedBy: 'user')]
    #[ORM\JoinColumn(name: 'employee_uuid', referencedColumnName: 'uuid', nullable: true)]
    private ?Employee $employee = null;

    public function __construct(private ?UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function hashPassword(string $password): string
    {
        if (null === $this->userPasswordHasher) {
            throw new \LogicException('Password hasher is not set.');
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

    public function getRoles(): array
    {
        return [];
    }

    public function getRolesEntities(): Collection
    {
        return $this->{self::RELATION_ROLES};
    }

    public function setEmployeeUuid(?string $employeeUuid): void
    {
        $this->{self::COLUMN_EMPLOYEE_UUID} = $employeeUuid;
    }

    public function setEmail(string $email): void
    {
        $this->{self::COLUMN_EMAIL} = $email;
    }

    public function setPassword(string $password): void
    {
        $this->{self::COLUMN_PASSWORD} = $this->hashPassword($password);
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

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(Employee $employee): void
    {
        $this->employee = $employee;
        if ($employee->getUser() !== $this) {
            $employee->setUser($this);
        }
    }
}
