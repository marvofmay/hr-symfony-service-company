<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Interface\Email;

use App\Module\System\Domain\Entity\Email;

interface EmailWriterInterface
{
    public function saveEmailInDB(Email $email): void;
}
