<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Entity;

use App\Common\Domain\Trait\AttributesEntityTrait;
use App\Common\Domain\Trait\RelationsEntityTrait;
use App\Common\Domain\Trait\TimeStampableTrait;
use App\Module\Company\Domain\Enum\Position\PositionEntityFieldEnum;
use App\Module\Company\Domain\Enum\Position\PositionEntityRelationFieldEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'position')]
#[ORM\Index(name: 'name', columns: ['name'])]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class Position
{
    use TimeStampableTrait;
    use AttributesEntityTrait;
    use RelationsEntityTrait;

    public const string ALIAS = 'position';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidInterface $uuid;

    #[ORM\Column(type: Types::STRING, length: 100, unique: true)]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    #[Assert\NotBlank]
    private bool $active;

    #[ORM\OneToMany(targetEntity: Employee::class, mappedBy: 'position', cascade: ['persist', 'remove'])]
    private Collection $employees;

    #[ORM\OneToMany(targetEntity: PositionDepartment::class, mappedBy: 'position', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $positionDepartments;

    private function __construct(UuidInterface $uuid, string $name, ?string $description = null, bool $active = false)
    {
        $this->uuid = Uuid::uuid7();
        $this->name = $name;
        $this->description = $description;
        $this->active = $active;

        $this->employees = new ArrayCollection();
        $this->positionDepartments = new ArrayCollection();
    }

    public static function create(string $name, ?string $description = null, bool $active = false): self
    {
        return new self(Uuid::uuid7(), $name, $description, $active);
    }

    public function getUUID(): UuidInterface
    {
        return $this->uuid;
    }

    public function getName(): string
    {
        return $this->{PositionEntityFieldEnum::NAME->value};
    }

    public function getDescription(): ?string
    {
        return $this->{PositionEntityFieldEnum::DESCRIPTION->value};
    }

    public function getEmployees(): Collection
    {
        return $this->{PositionEntityRelationFieldEnum::EMPLOYEES->value};
    }

    public function getPositionDepartments(): Collection
    {
        return $this->{PositionEntityRelationFieldEnum::POSITION_DEPARTMENTS->value};
    }

    public function rename(string $name): void
    {
        $this->name = $name;
    }

    public function updateDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function activate(): void
    {
        $this->active = true;
    }

    public function deactivate(): void
    {
        $this->active = false;
    }

    public function addDepartment(Department $department): void
    {
        foreach ($this->positionDepartments as $positionDepartment) {
            if ($positionDepartment->department === $department) {
                return;
            }
        }

        $positionDepartment = new PositionDepartment($this, $department);
        $this->positionDepartments->add($positionDepartment);
    }

    public function getDepartments(): Collection
    {
        return $this->positionDepartments->map(fn(PositionDepartment $pd) => $pd->department);
    }

    public function removeDepartment(Department $department): void
    {
        foreach ($this->positionDepartments as $key => $positionDepartment) {
            if ($positionDepartment->department === $department) {
                $this->positionDepartments->remove($key);
                break;
            }
        }
    }
}
