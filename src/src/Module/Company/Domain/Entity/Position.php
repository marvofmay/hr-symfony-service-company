<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Entity;

use App\Common\Domain\Interface\MappableEntityInterface;
use App\Common\Domain\Trait\AttributesEntityTrait;
use App\Common\Domain\Trait\RelationsEntityTrait;
use App\Common\Domain\Trait\TimeStampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Doctrine\UuidGenerator;
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
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $uuid;

    #[ORM\Column(type: Types::STRING, length: 100, unique: true)]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    #[Assert\NotBlank]
    private bool $active = true;

    #[ORM\OneToMany(targetEntity: Employee::class, mappedBy: 'position', cascade: ['persist', 'remove'])]
    private Collection $employees;

    #[ORM\OneToMany(targetEntity: PositionDepartment::class, mappedBy: 'position', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $positionDepartments;

    public function __construct()
    {
        $this->employees = new ArrayCollection();
        $this->positionDepartments = new ArrayCollection();
    }

    public function getUUID(): UuidInterface
    {
        return $this->uuid;
    }

    public function setUUID(UuidInterface $uuid): void
    {
        $this->uuid = $uuid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
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
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getEmployees(): Collection
    {
        return $this->employees;
    }

    public function setEmployees(Collection $employees): void
    {
        $this->employees = $employees;
    }

    public function getPositionDepartments(): Collection
    {
        return $this->positionDepartments;
    }

    public function setPositionDepartments(Collection $positionDepartments): void
    {
        $this->positionDepartments = $positionDepartments;
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
