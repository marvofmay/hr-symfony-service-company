<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Entity;

use App\Common\Domain\Trait\TimeStampableTrait;
use App\Module\Company\Domain\Entity\Employee;
use App\Module\System\Domain\Enum\Email\EmailStatusEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'email')]
#[ORM\Index(name: 'subject', columns: ['subject'])]
#[ORM\Index(name: 'sent_at', columns: ['sent_at'])]
#[ORM\Index(name: 'sender_uuid', columns: ['sender_uuid'])]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class Email
{
    use TimeStampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidInterface $uuid;

    #[ORM\ManyToOne(targetEntity: Employee::class)]
    #[ORM\JoinColumn(name: 'sender_uuid', referencedColumnName: 'uuid', nullable: true, onDelete: 'SET NULL')]
    private ?Employee $sender;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank]
    private string $subject;

    #[ORM\Column(type: Types::TEXT)]
    private string $message;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $templateName;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $renderedTemplate;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $context;

    #[ORM\Column(type: Types::JSON)]
    #[Assert\NotBlank]
    private array $recipients;

    #[ORM\OneToMany(targetEntity: File::class, mappedBy: 'email', cascade: ['persist', 'remove'])]
    private Collection $attachments;

    #[ORM\Column(enumType: EmailStatusEnum::class)]
    private EmailStatusEnum $status;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $failureReason = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeInterface $sentAt = null;

    private function __construct(
        string $subject,
        array $recipients,
        string $message = '',
        ?string $templateName = null,
        ?string $renderedTemplate = null,
        ?array $context = null,
        ?Employee $sender = null
    ) {
        $this->uuid = Uuid::uuid4();
        $this->subject = trim($subject);
        $this->recipients = $recipients;
        $this->message = trim($message);
        $this->templateName = $templateName;
        $this->renderedTemplate = $renderedTemplate;
        $this->sender = $sender;
        $this->attachments = new ArrayCollection();
        $this->status = EmailStatusEnum::PENDING;
        $this->context = $context;
        $this->createdAt = new \DateTime();
    }

    public static function create(
        string $subject,
        array $recipients,
        string $message = '',
        ?string $templateName = null,
        ?string $templateBody = null ,
        ?array $context = null,
        ?Employee $sender = null
    ): self
    {
        if (empty($subject)) {
            throw new \InvalidArgumentException('Email subject cannot be empty.');
        }

        if (empty($message) && empty($templateBody)) {
            throw new \InvalidArgumentException('Email message and template cannot both be empty.');
        }

        if (empty($recipients)) {
            throw new \InvalidArgumentException('Email must have at least one recipient.');
        }

        return new self(
            subject: $subject,
            recipients: $recipients,
            message: $message,
            templateName: $templateName,
            renderedTemplate: $templateBody,
            context: $context,
            sender: $sender
        );
    }

    public function markAsSent(): void
    {
        if ($this->status === EmailStatusEnum::SENT) {
            throw new \LogicException('Email is already marked as sent.');
        }

        $this->status = EmailStatusEnum::SENT;
        $this->sentAt = new \DateTimeImmutable();
        $this->failureReason = null;
    }

    public function markAsFailed(string $reason): void
    {
        $this->status = EmailStatusEnum::FAILED;
        $this->failureReason = trim($reason) ?: 'Unknow error';
        $this->sentAt = null;
    }

    public function wasSentSuccessfully(): bool
    {
        return $this->status === EmailStatusEnum::SENT;
    }

    public function addAttachment(File $file): void
    {
        if (!$this->attachments->contains($file)) {
            $this->attachments->add($file);
            $file->setEmail($this);
        }
    }

    public function getUUID(): UuidInterface
    {
        return $this->uuid;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getTemplateName(): ?string
    {
        return $this->templateName;
    }

    public function getRenderedTemplate(): ?string
    {
        return $this->renderedTemplate;
    }

    public function getContext(): ?array
    {
        return $this->context ?? [];
    }

    public function getRecipients(): array
    {
        return $this->recipients;
    }

    public function getSender(): ?Employee
    {
        return $this->sender;
    }

    public function setTemplateName(?string $templateName): void
    {
        $this->templateName = $templateName;
    }

    public function setRenderedTemplate(?string $content): void
    {
        $this->renderedTemplate = $content;
    }

    public function setContext(?array $context): void
    {
        $this->context = $context;
    }

    public function getAttachments(): Collection
    {
        return $this->attachments;
    }

    public function getStatus(): EmailStatusEnum
    {
        return $this->status;
    }

    public function getFailureReason(): ?string
    {
        return $this->failureReason;
    }

    public function getSentAt(): ?\DateTimeInterface
    {
        return $this->sentAt;
    }
}