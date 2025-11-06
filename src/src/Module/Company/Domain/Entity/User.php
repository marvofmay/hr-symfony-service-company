<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Entity;

use App\Common\Domain\Trait\AttributesEntityTrait;
use App\Common\Domain\Trait\TimeStampableTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'user')]
#[ORM\Index(name: 'email', columns: ['email'])]
#[ORM\UniqueConstraint(name: 'unique_email', columns: ['email'])]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class User implements PasswordAuthenticatedUserInterface, UserInterface
{
    use TimeStampableTrait;
    use AttributesEntityTrait;

    public const COLUMN_UUID = 'uuid';
    public const COLUMN_EMPLOYEE_UUID = 'employee_uuid';
    public const COLUMN_EMAIL = 'email';
    public const COLUMN_PASSWORD = 'password';
    public const COLUMN_CREATED_AT = 'createdAt';
    public const COLUMN_UPDATED_AT = 'updatedAt';
    public const COLUMN_DELETED_AT = 'deletedAt';
    public const RELATION_ROLES = 'roles';
    public const RELATION_EMPLOYEE = 'employee';

    public const SOFT_DELETED_AT = 'soft';
    public const HARD_DELETED_AT = 'hard';

    public const string ALIAS = 'user';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidInterface $uuid;

    #[ORM\Column(type: 'uuid', nullable: true)]
    #[Assert\NotBlank]
    private ?UuidInterface $employee_uuid = null;

    #[ORM\Column(type: Types::STRING, length: 100, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private string $email;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank]
    private string $password;

    #[ORM\OneToOne(targetEntity: Employee::class, inversedBy: 'user', cascade: ['remove'])]
    #[ORM\JoinColumn(name: 'employee_uuid', referencedColumnName: 'uuid', nullable: true, onDelete: 'CASCADE')]
    private ?Employee $employee = null;

    public function __construct()
    {
    }

    public static function create(string $email): self
    {
        $self = new self();
        $self->uuid = Uuid::uuid7();
        $self->email = $email;

        return $self;
    }

    public function getUUID(): UuidInterface
    {
        return $this->{self::COLUMN_UUID};
    }

    public function getEmployeeUUID(): ?string
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
        if (null === $this->employee) {
            return ['Pracownik techniczny systemu - nie powiązany z pracownikiem firmy'];
        }

        return [$this->employee->getRole()->getName()];
    }

    public function getRolesEntities(): Collection
    {
        return $this->{self::RELATION_ROLES};
    }


    public function setEmail(string $email): void
    {
        $this->{self::COLUMN_EMAIL} = $email;
    }

    public function setPassword(string $hashedPassword): void
    {
        $this->{self::COLUMN_PASSWORD} = $hashedPassword;
    }



    #[\Deprecated]
    public function eraseCredentials(): void
    {
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
