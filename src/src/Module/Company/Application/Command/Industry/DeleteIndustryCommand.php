<?php

namespace App\Module\Company\Application\Command\Industry;

use App\Module\Company\Domain\Entity\Industry;

readonly class DeleteIndustryCommand
{
    public function __construct(private Industry $industry)
    {
    }

    public function getIndustry(): Industry
    {
        return $this->industry;
    }
}
