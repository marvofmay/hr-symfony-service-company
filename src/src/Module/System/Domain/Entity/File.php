<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Entity;

use App\Common\Domain\Enum\FileExtensionEnum;
use App\Common\Domain\Enum\FileKindEnum;
use App\Common\Domain\Trait\AttributesEntityTrait;
use App\Common\Domain\Trait\TimeStampableTrait;
use App\Module\Company\Domain\Entity\Employee;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity]
#[ORM\Table(name: 'file')]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class File
{
    use TimeStampableTrait;
    use AttributesEntityTrait;

    public const COLUMN_UUID = 'uuid';
    public const COLUMN_FILE_NAME = 'fileName';
    public const COLUMN_FILE_PATH = 'filePath';
    public const COLUMN_EXTENSION = 'extension';
    public const COLUMN_DESCRIPTION = 'description';
    public const COLUMN_KIND = 'kind';
    public const COLUMN_CREATED_AT = 'createdAt';
    public const COLUMN_UPDATED_AT = 'updatedAt';
    public const COLUMN_DELETED_AT = 'deletedAt';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $uuid;

    #[ORM\Column(type: Types::STRING, length: 150)]
    private string $fileName;

    #[ORM\Column(type: Types::STRING, length: 250)]
    private string $filePath;

    #[ORM\Column(type: Types::STRING, enumType: FileExtensionEnum::class)]
    private FileExtensionEnum $extension;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::STRING, enumType: FileKindEnum::class)]
    private FileKindEnum $kind;

    #[ORM\ManyToOne(targetEntity: Employee::class, inversedBy: 'files')]
    #[ORM\JoinColumn(name: 'employee_uuid', referencedColumnName: 'uuid', nullable: true, onDelete: 'CASCADE')]
    private ?Employee $employee = null;

    #[ORM\OneToOne(targetEntity: Import::class, mappedBy: 'file')]
    private ?Import $import = null;

    public function getUUID(): UuidInterface
    {
        return $this->uuid;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function setFilePath(string $filePath): self
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function getExtension(): FileExtensionEnum
    {
        return $this->extension;
    }

    public function setExtension(FileExtensionEnum $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getKind(): FileKindEnum
    {
        return $this->kind;
    }

    public function setKind(FileKindEnum $kind): void
    {
        $this->kind = $kind;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): void
    {
        $this->employee = $employee;
    }

    public function getImport(): ?Import
    {
        return $this->import;
    }

    public function setImport(?Import $import): void
    {
        $this->import = $import;
    }

    public function toArray(): array
    {
        return [
            self::COLUMN_UUID => $this->uuid->toString(),
            self::COLUMN_FILE_NAME => $this->fileName,
            self::COLUMN_FILE_PATH => $this->filePath,
            self::COLUMN_DESCRIPTION => $this->description,
            self::COLUMN_CREATED_AT => $this->createdAt->format('Y-m-d H:i:s'),
            self::COLUMN_UPDATED_AT => $this->updatedAt->format('Y-m-d H:i:s'),
            self::COLUMN_DELETED_AT => $this->deletedAt->format('Y-m-d H:i:s'),
        ];
    }
}
