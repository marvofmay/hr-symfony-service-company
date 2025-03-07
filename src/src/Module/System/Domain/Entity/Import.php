<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Entity;

use App\Common\Domain\Trait\AttributesEntityTrait;
use App\Common\Domain\Trait\TimestampableTrait;
use App\Module\Company\Domain\Entity\Employee;
use App\Module\System\Domain\Enum\ImportKindEnum;
use App\Module\System\Domain\Enum\ImportStatusEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity]
#[ORM\Table(name: 'import')]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class Import
{
    use TimestampableTrait;
    use AttributesEntityTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $uuid;

    #[ORM\ManyToOne(targetEntity: Employee::class, inversedBy: 'imports')]
    #[ORM\JoinColumn(name: 'employee_uuid', referencedColumnName: 'uuid', nullable: true, onDelete: 'CASCADE')]
    private ?Employee $employee;

    #[ORM\Column(type: 'string', enumType: ImportKindEnum::class)]
    private ImportKindEnum $kind;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $startedAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $stoppedAt = null;

    #[ORM\Column(type: 'string', enumType: ImportStatusEnum::class)]
    private ImportStatusEnum $status = ImportStatusEnum::PENDING;

    #[ORM\OneToMany(targetEntity: ImportLog::class, mappedBy: 'import', cascade: ['persist', 'remove'])]
    private Collection $logs;

    #[ORM\OneToOne(targetEntity: File::class, inversedBy: 'import')]
    #[ORM\JoinColumn(name: 'file_uuid', referencedColumnName: 'uuid', unique: true, nullable: false, onDelete: 'CASCADE')]
    private File $file;

    public function __construct()
    {
        $this->logs = new ArrayCollection();
    }

    public function markAsCompleted(): void
    {
        $this->stoppedAt = new \DateTimeImmutable();
        $this->status = ImportStatusEnum::DONE;
    }

    public function markAsFailed(): void
    {
        $this->stoppedAt = new \DateTimeImmutable();
        $this->status = ImportStatusEnum::FAILED;
    }

    public function getFile(): File
    {
        return $this->file;
    }

    public function setFile(File $file): void
    {
        $this->file = $file;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): void
    {
        $this->employee = $employee;
    }
}