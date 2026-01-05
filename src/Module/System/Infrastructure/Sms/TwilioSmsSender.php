<?php

declare(strict_types=1);

namespace App\Module\System\Infrastructure\Sms;

use App\Module\Company\Domain\Interface\Sms\SmsSenderInterface;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Twilio\Rest\Client;

#[AsAlias(SmsSenderInterface::class)]
class TwilioSmsSender implements SmsSenderInterface
{
    private Client $client;
    private string $from;

    public function __construct(string $sid, string $token, string $from)
    {
        $this->client = new Client($sid, $token);
        $this->from = $from;
    }

    public function send(string $to, string $message): void
    {
        $this->client->messages->create(
            $to,
            [
                'from' => $this->from,
                'body' => $message,
            ]
        );
    }
}