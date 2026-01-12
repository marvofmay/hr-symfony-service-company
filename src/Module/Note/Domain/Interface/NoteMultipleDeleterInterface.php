<?php

declare(strict_types=1);

namespace App\Module\Note\Domain\Interface;

use Doctrine\Common\Collections\Collection;

interface NoteMultipleDeleterInterface
{
    public function multipleDelete(Collection $notes): void;
}
