<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Event\Industry;

final class IndustryImportedEvent extends IndustryEvent
{
    public function __construct(public readonly array $data) {}

    public function getData(): array
    {
        return $this->data;
    }
}