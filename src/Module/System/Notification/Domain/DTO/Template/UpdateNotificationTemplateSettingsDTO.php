<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\DTO\Template;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateNotificationTemplateSettingsDTO
{
    #[Assert\NotBlank]
    public string $eventName;

    #[Assert\NotBlank]
    public string $channelCode;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $title;

    #[Assert\NotBlank]
    #[Assert\Length(max: 500)]
    public string $content;

    public bool $searchDefault = false;
    public bool $markAsActive = false;
}
