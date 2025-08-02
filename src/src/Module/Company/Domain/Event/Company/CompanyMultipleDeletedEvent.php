<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Event\Company;

use App\Common\Domain\Interface\DomainEventInterface;

final readonly class CompanyMultipleDeletedEvent implements DomainEventInterface
{
    public function __construct(public array $uuids,) {}
}