<?php

declare(strict_types=1);

namespace App\Module\System\Application\Console\FakeData\Data;

final class Company
{
    public static function getDefaultData(): array
    {
        return [
            'fullName' => 'Feture Technology',
            'shortName' => 'FT',
            'nip' => '9316831327',
            'regon' => '9316831327',
            'description' => '',
            'industryUUID' => '5c2c8ba0-856c-440b-ac09-edeb53a95373',
            'active' => true,
            'phones' => [
                '155-555-555',
                '255-555-555',
                '355-555-555',
            ],
            'emails' => [
                'futuretechnnology_1@example.com',
                'futuretechnnology_2@example.com',
                'futuretechnnology_3@example.com',
            ],
            'websites' => [
                'http://futuretechnology_1.com',
                'http://futuretechnology_2.com',
            ],
            'address' => [
                'street' => 'Wiejska 1',
                'postcode' => '11-111',
                'city' => 'Gdańsk',
                'country' => 'Polska',
                'active' => true,
            ],
        ];
    }
}