<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Position;

use App\Common\Domain\Interface\CommandInterface;

final readonly class CreatePositionCommand implements CommandInterface
{
    public const string POSITION_NAME = 'name';
    public const string POSITION_DESCRIPTION = 'description';
    public const string POSITION_ACTIVE = 'active';
    public const string DEPARTMENTS_UUIDS = 'departmentsUUIDs';

    public function __construct(public string $name, public ?string $description, public ?bool $active, public array $departmentsUUIDs)
    {
    }
}
