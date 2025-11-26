<?php

declare(strict_types=1);

namespace App\Module\System\Application\Console\FakeData\Data;

use App\Module\System\Application\Console\FakeData\Data\Company as CompanyFakeData;

final readonly class Department
{
    public function __construct()
    {
    }

    public function getDefaultData(): array
    {
        return [
            'companyName' => CompanyFakeData::COMPANY_NAME_FUTURE_TECHNOLOGY,
            'name' => 'Departament aplikacji internetowych',
            'internalCode' => 'dai',
            'description' => 'Departament aplikacji internetowych - opis',
            'departmentUUID' => null,
            'active' => true,
            'phones' => [
                '111-555-555',
                '222-555-555',
                '333-555-555',
            ],
            'emails' => [
                'dai_1@example.com',
                'dai_2@example.com',
                'dai_3@example.com',
            ],
            'websites' => [
                'http://dai_1.futuretechnology_1.com',
                'http://dai_2.futuretechnology_2.com',
            ],
            'address' => [
                'street' => 'Opolska 12',
                'postcode' => '11-111',
                'city' => 'Gdańsk',
                'country' => 'Polska',
                'active' => true,
            ],
        ];
    }
}
