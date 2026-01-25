<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Aggregate\ValueObject;

use App\Module\Company\Domain\Enum\Contact\ContactTypeEnum;

final class Contact
{
    private ContactTypeEnum $type;
    private string $value;

    public function __construct(ContactTypeEnum $type, string $value)
    {
        $this->type = $type;
        $this->value = $value;
    }

    public function getType(): ContactTypeEnum
    {
        return $this->type;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
