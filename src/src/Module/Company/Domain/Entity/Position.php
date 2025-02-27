<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Entity;

use App\Common\Trait\AttributesEntityTrait;
use App\Common\Trait\RelationsEntityTrait;
use App\Common\Trait\TimestampableTrait;
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
#[ORM\Table(name: 'position')]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class Position
{
    use TimestampableTrait;
    use AttributesEntityTrait;
    use RelationsEntityTrait;

    public const COLUMN_UUID = 'uuid';
    public const COLUMN_NAME = 'name';
    public const COLUMN_DESCRIPTION = 'description';
    public const COLUMN_ACTIVE = 'active';

    public const COLUMN_CREATED_AT = 'createdAt';
    public const COLUMN_UPDATED_AT = 'updatedAt';
    public const COLUMN_DELETED_AT = 'deletedAt';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups('position_info')]
    private UuidInterface $uuid;

    #[ORM\Column(type: Types::STRING, length: 200)]
    #[Assert\NotBlank]
    #[Groups('position_info')]
    private string $name;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups('position_info')]
    private ?string $description = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    #[Assert\NotBlank]
    #[Groups('position_info')]
    private bool $active;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups('position_info')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups('position_info')]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups('position_info')]
    private ?\DateTimeInterface $deletedAt = null;

    #[ORM\ManyToMany(targetEntity: Department::class, inversedBy: "positions")]
    #[ORM\JoinTable(name: "position_department")]
    #[ORM\JoinColumn(name: "position_uuid", referencedColumnName: "uuid")]
    #[ORM\InverseJoinColumn(name: "department_uuid", referencedColumnName: "uuid")]
    #[Groups('position_info')]
    public Collection $departments;

    public function __construct()
    {
        $this->departments = new ArrayCollection();
    }

    public function getUUID(): UuidInterface
    {
        return $this->{self::COLUMN_UUID};
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
        return $this->{self::COLUMN_DESCRIPTION};
    }

    public function setDescription(?string $description): void
    {
        $this->{self::COLUMN_DESCRIPTION} = $description;
    }

    public function getActive(): bool
    {
        return $this->{self::COLUMN_ACTIVE};
    }

    public function setActive(bool $active): void
    {
        $this->{self::COLUMN_ACTIVE} = $active;
    }

    public function getDepartments(): Collection
    {
        return $this->departments;
    }

    public function addDepartment(Department $department): void
    {
        if (!$this->departments->contains($department)) {
            $this->departments->add($department);
            $department->addPosition($this);
        }
    }

    public function removeDepartment(Department $department): void
    {
        if ($this->departments->removeElement($department)) {
            $department->removePosition($this);
        }
    }
}
