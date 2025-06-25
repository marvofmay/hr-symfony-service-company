<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Event\Industry;

use App\Module\Company\Application\Query\Industry\ListIndustriesQuery;

final class IndustryListedEvent extends IndustryEvent
{
    public function __construct(public readonly ListIndustriesQuery $query) {}

    public function getData(): array
    {
        return ['query' => $this->query];
    }
}