<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Entity;

use App\Common\Domain\Interface\MappableEntityInterface;
use App\Common\Domain\Trait\AttributesEntityTrait;
use App\Common\Domain\Trait\RelationsEntityTrait;
use App\Common\Domain\Trait\TimeStampableTrait;
use App\Module\Company\Domain\Enum\Role\RoleEntityFieldEnum;
use App\Module\System\Domain\Entity\Access;
use App\Module\System\Domain\Entity\Permission;
use App\Module\System\Domain\Entity\RoleAccess;
use App\Module\System\Domain\Entity\RoleAccessPermission;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'role')]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: false)]
class Role  implements MappableEntityInterface
{
    use TimeStampableTrait;
    use AttributesEntityTrait;
    use RelationsEntityTrait;

    public const ALIAS = 'role';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $uuid;

    #[ORM\Column(type: Types::STRING, length: 100, unique: true)]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(targetEntity: Employee::class, mappedBy: 'role', cascade: ['persist', 'remove'])]
    private Collection $employees;

    #[ORM\OneToMany(targetEntity: RoleAccess::class, mappedBy: 'role', cascade: ['persist', 'remove'])]
    private Collection $roleAccesses;

    #[ORM\OneToMany(targetEntity: RoleAccessPermission::class, mappedBy: 'role', cascade: ['persist', 'remove'])]
    private Collection $accessPermissions;

    public function __construct()
    {
        $this->uuid = Uuid::uuid4();
        $this->createdAt = new \DateTime();

        $this->employees = new ArrayCollection();
        $this->roleAccesses = new ArrayCollection();
        $this->accessPermissions = new ArrayCollection();
    }

    public function getUUID(): UuidInterface
    {
        return $this->{RoleEntityFieldEnum::UUID->value};
    }

    public function setUuid(UuidInterface $uuid): void
    {
        $this->{RoleEntityFieldEnum::UUID->value} = $uuid;
    }

    public function getName(): string
    {
        return $this->{RoleEntityFieldEnum::NAME->value};
    }

    public function setName(string $name): void
    {
        $this->{RoleEntityFieldEnum::NAME->value} = $name;
    }

    public function getDescription(): ?string
    {
        return $this->{RoleEntityFieldEnum::DESCRIPTION->value};
    }

    public function setDescription(?string $description): void
    {
        $this->{RoleEntityFieldEnum::DESCRIPTION->value} = $description;
    }

    public function getEmployees(): Collection
    {
        return $this->employees;
    }

    public function getRoleAccesses(): Collection
    {
        return $this->roleAccesses;
    }

    public function addAccess(Access $access): void
    {
        foreach ($this->roleAccesses as $roleAccess) {
            if ($roleAccess->getAccess() === $access) {
                return;
            }
        }

        $roleAccess = new RoleAccess($this, $access);
        $this->roleAccesses->add($roleAccess);
    }

    public function removeAccess(Access $access): void
    {
        foreach ($this->roleAccesses as $roleAccess) {
            if ($roleAccess->getAccess() === $access) {
                $this->roleAccesses->removeElement($roleAccess);
                break;
            }
        }
    }

    public function addAccessPermission(Access $access, Permission $permission): void
    {
        $relation = new RoleAccessPermission($this, $access, $permission);
        $this->accessPermissions->add($relation);
    }

    public function getAccesses(): Collection
    {
        return $this->getRoleAccesses()->map(fn (RoleAccess $ra) => $ra->getAccess());
    }
}
