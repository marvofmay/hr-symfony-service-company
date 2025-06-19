<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Position;

use Doctrine\Common\Collections\Collection;

final class CreatePositionCommand
{
    public function __construct(public string $name, public ?string $description, public ?bool $active, public Collection $departments)
    {
    }
}
