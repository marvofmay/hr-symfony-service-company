<?php

declare(strict_types=1);

namespace App\Common\Domain\Trait;

use Doctrine\DBAL\Schema\DefaultExpression\CurrentTimestamp;
use Doctrine\ORM\Mapping as ORM;

trait TimeStampableTrait
{
    #[ORM\Column(
        type: 'datetime',
        options: ['default' => new CurrentTimestamp()]
    )]
    public \DateTimeInterface $createdAt;

    #[ORM\Column(
        type: 'datetime',
        nullable: true,
        options: ['default' => new CurrentTimestamp()]
    )]
    public ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(
        type: 'datetime',
        nullable: true,
        options: ['default' => new CurrentTimestamp()]
    )]
    public ?\DateTimeInterface $deletedAt = null;

    #[ORM\PrePersist]
    public function setCreatedAt(): void
    {
        $this->createdAt = new \DateTime();
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAt(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }
}
