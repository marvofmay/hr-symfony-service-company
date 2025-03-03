<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Entity;

use App\Common\Domain\Trait\AttributesEntityTrait;
use App\Common\Domain\Trait\TimestampableTrait;
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

    public const COLUMN_UUID = 'uuid';
    public const COLUMN_COMPANY_UUID = 'company_uuid';
    public const COLUMN_DEPARTMENT_UUID = 'department_uuid';
    public const COLUMN_NAME = 'name';
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

    #[ORM\ManyToOne(targetEntity: Company::class)]
    #[ORM\JoinColumn(name: 'company_uuid', referencedColumnName: 'uuid', nullable: false, onDelete: 'CASCADE')]
    #[Groups('department_info')]
    private Company $company;

    #[ORM\ManyToOne(targetEntity: Department::class)]
    #[ORM\JoinColumn(name: 'department_uuid', referencedColumnName: 'uuid', nullable: true, onDelete: 'CASCADE')]
    #[Groups('department_info')]
    private ?Department $parentDepartment = null;

    #[ORM\Column(type: Types::STRING, length: 1000)]
    #[Assert\NotBlank]
    #[Groups('department_info')]
    private string $name;

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

    #[ORM\ManyToMany(targetEntity: Position::class, mappedBy: 'departments')]
    private Collection $positions;

    public function __construct()
    {
        $this->positions = new ArrayCollection();
    }

    public function getUuid(): UuidInterface
    {
        return $this->{self::COLUMN_UUID};
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): void
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

    public function getName(): string
    {
        return $this->{self::COLUMN_NAME};
    }

    public function setName(string $name): void
    {
        $this->{self::COLUMN_NAME} = $name;
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

    public function toArray(): array {
        return [
            'uuid' => $this->getUuid()->toString(),
            'name' => $this->getName(),
            'active' => $this->getActive(),
            'company' => $this->getCompany(),
            'parentDepartment' => $this->getParentDepartment() ? $this->getParentDepartment()->toArray() : null,
            'createdAt' => $this->getCreatedAt(),
            'updatedAt' => $this->getUpdatedAt(),
            'deletedAt' => $this->getDeletedAt(),
        ];
    }
}
