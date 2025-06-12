<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Industry;

use App\Common\Validator\Constraints\MinMaxLength;
use App\Common\Validator\Constraints\NotBlank;
use App\Module\Company\Structure\Validator\Constraints\Industry\UniqueIndustryName;

class CreateDTO
{
    #[NotBlank(message: [
        'text' => 'industry.name.required',
        'domain' => 'industries',
    ])]
    #[MinMaxLength(min: 3, max: 50, message: [
        'tooShort' => 'industry.name.minimumLength',
        'tooLong' => 'industry.name.maximumLength',
        'domain' => 'industries',
    ])]
    #[UniqueIndustryName]
    public string $name = '';

    public ?string $description = null;

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
