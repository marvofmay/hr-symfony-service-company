<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Entity;

use App\Common\Domain\Interface\MappableEntityInterface;
use App\Common\Domain\Trait\AttributesEntityTrait;
use App\Common\Domain\Trait\RelationsEntityTrait;
use App\Common\Domain\Trait\TimeStampableTrait;
use App\Module\Company\Domain\Enum\ContractType\ContractTypeEntityFieldEnum;
use App\Module\Company\Domain\Enum\ContractType\ContractTypeEntityRelationFieldEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'contract_type')]
#[ORM\Index(name: 'name', columns: ['name'])]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class ContractType implements MappableEntityInterface
{
    use TimeStampableTrait;
    use AttributesEntityTrait;
    use RelationsEntityTrait;

    public const string ALIAS = 'contractType';

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
    private bool $active;

    #[ORM\OneToMany(targetEntity: Employee::class, mappedBy: 'contractType', cascade: ['persist', 'remove'])]
    private Collection $employees;

    public function __construct()
    {
        $this->employees = new ArrayCollection();
    }

    public function getUUID(): UuidInterface
    {
        return $this->{ContractTypeEntityFieldEnum::UUID->value};
    }

    public function getName(): string
    {
        return $this->{ContractTypeEntityFieldEnum::NAME->value};
    }

    public function setName(string $name): void
    {
        $this->{ContractTypeEntityFieldEnum::NAME->value} = $name;
    }

    public function getDescription(): ?string
    {
        return $this->{ContractTypeEntityFieldEnum::DESCRIPTION->value};
    }

    public function setDescription(?string $description): void
    {
        $this->{ContractTypeEntityFieldEnum::DESCRIPTION->value} = $description;
    }

    public function getActive(): bool
    {
        return $this->{ContractTypeEntityFieldEnum::ACTIVE->value};
    }

    public function setActive(bool $active): void
    {
        $this->{ContractTypeEntityFieldEnum::ACTIVE->value} = $active;
    }

    public function getEmployees(): Collection
    {
        return $this->{ContractTypeEntityRelationFieldEnum::EMPLOYEES->value};
    }
}
