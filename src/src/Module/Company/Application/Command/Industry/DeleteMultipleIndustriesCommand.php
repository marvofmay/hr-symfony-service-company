<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Industry;

use Doctrine\Common\Collections\Collection;

class DeleteMultipleIndustriesCommand
{
    public function __construct(public Collection $industries)
    {
    }
}
