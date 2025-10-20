<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Event\Company;

use App\Module\Company\Application\Event\Event;
use App\Module\Company\Domain\Entity\Company;

class CompanyEvent extends Event
{
    public function getEntityClass(): string
    {
        return Company::class;
    }
}
