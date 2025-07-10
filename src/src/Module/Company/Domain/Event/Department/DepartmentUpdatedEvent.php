<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Event\Department;

use App\Common\Domain\Interface\DomainEventInterface;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\Address;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\CompanyUUID;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\Emails;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\Phones;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\Websites;
use App\Module\Company\Domain\Aggregate\Department\ValueObject\DepartmentUUID;
use App\Module\Company\Domain\Aggregate\Department\ValueObject\Name;

final readonly class DepartmentUpdatedEvent implements DomainEventInterface
{
    public function __construct(
        DepartmentUUID  $uuid,
        CompanyUUID     $companyUUID,
        Name            $name,
        Address         $address,
        bool            $active = true,
        ?string         $description = null,
        ?Phones         $phones = null,
        ?Emails         $emails = null,
        ?Websites       $websites = null,
        ?DepartmentUUID $parentDepartmentUUID = null
    )
    {
    }
}