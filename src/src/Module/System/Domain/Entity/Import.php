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

    public const COLUMN_UUID = 'uuid';
    public const COLUMN_STATUS = 'status';
    public const COLUMN_KIND = 'kind';
    public const COLUMN_STARTED_AT = 'startedAt';
    public const COLUMN_STOPPED_AT = 'stoppedAt';

    public const COLUMN_CREATED_AT = 'createdAt';
    public const COLUMN_UPDATED_AT = 'updatedAt';
    public const COLUMN_DELETED_AT = 'deletedAt';
    public const RELATION_EMPLOYEE = 'employe';
    public const RELATION_FILE = 'file';
    public const RELATION_LOGS = 'logs';

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
    private Collection $importLogs;

    #[ORM\OneToOne(targetEntity: ImportReport::class, mappedBy: 'import', cascade: ['persist', 'remove'])]
    private ?ImportReport $report;

    #[ORM\OneToOne(targetEntity: File::class, inversedBy: 'import')]
    #[ORM\JoinColumn(name: 'file_uuid', referencedColumnName: 'uuid', unique: true, nullable: false, onDelete: 'CASCADE')]
    private File $file;

    public function __construct()
    {
        $this->importLogs = new ArrayCollection();
    }

    public function getUUID(): UuidInterface
    {
        return $this->uuid;
    }

    public function getKind(): ImportKindEnum
    {
        return $this->kind;
    }

    public function setKind(ImportKindEnum $kind): void
    {
        $this->kind = $kind;
    }

    public function getStatus(): ImportStatusEnum
    {
        return $this->status;
    }

    public function setStatus(ImportStatusEnum $status): void
    {
        $this->status = $status;
    }

    public function markAsPending(): void
    {
        $this->startedAt = new \DateTime();
        $this->status = ImportStatusEnum::PENDING;
    }

    public function markAsDone(): void
    {
        $this->stoppedAt = new \DateTime();
        $this->status = ImportStatusEnum::DONE;
    }

    public function markAsFailed(): void
    {
        $this->stoppedAt = new \DateTime();
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

    public function getReport(): ?ImportReport
    {
        return $this->report;
    }

    public function setReport(ImportReport $report): void
    {
        $this->report = $report;
        $report->setImport($this);
    }

    public function getImportLogs(): Collection
    {
        return $this->importLogs;
    }

    public function getStartedAt(): \DateTimeInterface
    {
        return $this->startedAt;
    }

    public function setStartedAt(\DateTimeInterface $startedAt): void
    {
        $this->startedAt = $startedAt;
    }

    public function getStoppedAt(): ?\DateTimeInterface
    {
        return $this->stoppedAt;
    }

    public function setStoppedAt(?\DateTimeInterface $stoppedAt): void
    {
        $this->stoppedAt = $stoppedAt;
    }

    public function addImportLog(ImportLog $importLog): void
    {
        if (!$this->importLogs->contains($importLog)) {
            $this->importLogs->add($importLog);
            $importLog->setImport($this);
        }
    }

    public function removeImportLog(ImportLog $importLog): void
    {
        if ($this->importLogs->contains($importLog)) {
            $this->importLogs->removeElement($importLog);
            if ($importLog->getImport() === $this) {
                $importLog->setImport(null);
            }
        }
    }

    public function toArray(): array
    {
        return [
            Import::COLUMN_UUID => $this->uuid->toString(),
            Import::COLUMN_KIND => $this->kind,
            Import::COLUMN_STATUS => $this->status,
            Import::RELATION_LOGS => $this->importLogs->toArray(),
            Import::RELATION_FILE => $this->file->toArray(),
            Import::COLUMN_STARTED_AT => $this->startedAt->format('Y-m-d H:i:s'),
            Import::COLUMN_STOPPED_AT => $this->stoppedAt->format('Y-m-d H:i:s'),
            Import::COLUMN_CREATED_AT => $this->createdAt->format('Y-m-d H:i:s'),
            Import::COLUMN_UPDATED_AT => $this->updatedAt->format('Y-m-d H:i:s'),
            Import::COLUMN_DELETED_AT => $this->deletedAt->format('Y-m-d H:i:s'),
        ];
    }
}
