<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Company;

use Doctrine\Common\Collections\Collection;

class DeleteMultipleCompaniesCommand
{
    public function __construct(public Collection $companies)
    {
    }
}
