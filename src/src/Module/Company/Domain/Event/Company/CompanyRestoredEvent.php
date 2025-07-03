<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Event\Company;

use App\Common\Domain\Interface\DomainEventInterface;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\CompanyUUID;

final readonly class CompanyRestoredEvent implements DomainEventInterface
{
    public function __construct(public CompanyUUID $uuid,) {}
}