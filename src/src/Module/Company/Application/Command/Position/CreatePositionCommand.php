<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Position;

use App\Common\Domain\Interface\CommandInterface;

final readonly class CreatePositionCommand implements CommandInterface
{
    public const string NAME = 'name';
    public const string DESCRIPTION = 'description';
    public const string ACTIVE = 'active';
    public const string DEPARTMENTS_UUIDS = 'departmentsUUIDs';

    public function __construct(public string $name, public ?string $description, public ?bool $active, public array $departmentsUUIDs)
    {
    }
}
