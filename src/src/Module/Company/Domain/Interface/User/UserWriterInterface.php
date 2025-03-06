<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\User;

use App\Module\Company\Domain\Entity\User;

interface UserWriterInterface
{
    public function deleteUserInDB(User $user): void;
}
