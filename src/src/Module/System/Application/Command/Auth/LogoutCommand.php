<?php

declare(strict_types=1);

namespace App\Module\System\Application\Command\Auth;

use Symfony\Component\HttpFoundation\Request;

final readonly class LogoutCommand
{
    public const string TOKEN_UUID = 'tokenUUID';

    public function __construct(public Request $request) {}
}