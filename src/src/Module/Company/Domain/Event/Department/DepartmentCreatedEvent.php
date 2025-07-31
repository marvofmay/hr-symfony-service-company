<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Event\Department;

use App\Common\Domain\Interface\DomainEventInterface;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\CompanyUUID;
use App\Module\Company\Domain\Aggregate\Department\ValueObject\DepartmentUUID;
use App\Module\Company\Domain\Aggregate\Department\ValueObject\Name;
use App\Module\Company\Domain\Aggregate\ValueObject\Address;
use App\Module\Company\Domain\Aggregate\ValueObject\Emails;
use App\Module\Company\Domain\Aggregate\ValueObject\Phones;
use App\Module\Company\Domain\Aggregate\ValueObject\Websites;

final readonly class DepartmentCreatedEvent implements DomainEventInterface
{
    public function __construct(
        public DepartmentUUID $uuid,
        public CompanyUUID    $companyUUID,
        public Name           $name,
        public Address        $address,
        public bool           $active,
        public ?string        $description = null,
        public ?Phones        $phones = null,
        public ?Emails        $emails = null,
        public ?Websites      $websites = null,
        public ?DepartmentUUID $parentDepartmentUUID = null,
    )
    {
    }
}