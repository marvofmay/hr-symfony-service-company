<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Sms;

interface SmsSenderInterface
{
    public function send(string $to, string $message): void;
}