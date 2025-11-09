<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Entity;

use App\Common\Domain\Trait\AttributesEntityTrait;
use App\Common\Domain\Trait\RelationsEntityTrait;
use App\Common\Domain\Trait\TimeStampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'contract_type')]
#[ORM\Index(name: 'name', columns: ['name'])]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class ContractType
{
    use TimeStampableTrait;
    use AttributesEntityTrait;
    use RelationsEntityTrait;

    public const string ALIAS = 'contract_type';

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
    private bool $active = false;

    #[ORM\OneToMany(targetEntity: Employee::class, mappedBy: 'contractType', cascade: ['persist', 'remove'])]
    private Collection $employees;

    private function __construct()
    {
        $this->employees = new ArrayCollection();
    }

    public static function create(string $name, ?string $description = null, bool $active = false): self
    {
        $self = new self();
        $self->uuid = Uuid::uuid4();
        $self->name = $name;
        $self->description = $description;
        $self->active = $active;

        return $self;
    }


    public function rename(string $newName): void
    {
        if ($this->name === $newName) {
            return;
        }

        $this->name = $newName;
    }

    public function updateDescription(?string $newDescription): void
    {
        $this->description = $newDescription;
    }

    public function activate(): void
    {
        $this->active = true;
    }

    public function deactivate(): void
    {
        $this->active = false;
    }

    public function getUUID(): UuidInterface
    {
        return $this->uuid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getEmployees(): Collection
    {
        return $this->employees;
    }
}
