<?php

declare(strict_types=1);

namespace App\Module\Note\Domain\DTO;

use App\Module\Note\Domain\Trait\TitleContentPriorityTrait;

final class UpdateDTO
{
    use TitleContentPriorityTrait;
}
