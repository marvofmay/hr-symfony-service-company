<?php

declare(strict_types=1);

namespace App\Module\Note\Application\Query;

use App\Common\Domain\Interface\QueryInterface;

final class GetNotePDFQuery implements QueryInterface
{
    public const string NOTE_UUID = 'noteUUID';

    public function __construct(public string $noteUUID)
    {
    }
}
