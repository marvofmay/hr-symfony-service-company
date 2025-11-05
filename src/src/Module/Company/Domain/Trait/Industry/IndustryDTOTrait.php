<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Trait\Industry;

use App\Common\Validator\Constraints\MinMaxLength;
use App\Common\Validator\Constraints\NotBlank;

trait IndustryDTOTrait
{
    #[NotBlank(message: [
        'text' => 'industry.name.required',
        'domain' => 'industries',
    ])]
    #[MinMaxLength(min: 3, max: 100, message: [
        'tooShort' => 'industry.name.minimumLength',
        'tooLong' => 'industry.name.maximumLength',
        'domain' => 'industries',
    ])]
    public string $name = '' {
        get {
            return $this->name;
        }
    }

    public ?string $description = null {
        get {
            return $this->description;
        }
    }
}
