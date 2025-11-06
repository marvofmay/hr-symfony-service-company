<?php

namespace App\tests\unit\module\company\domain\entity;

use App\Module\Company\Domain\Entity\ContractType;
use PHPUnit\Framework\TestCase;

class ContractTypeTest extends TestCase
{
    public function testItSetsAndGetsProperties(): void
    {
        $contractType = ContractType::create('Umowa o pracę', 'Pełny etat', true);

        $this->assertSame('Umowa o pracę', $contractType->getName());
        $this->assertSame('Pełny etat', $contractType->getDescription());
        $this->assertTrue($contractType->isActive());
        $this->assertCount(0, $contractType->getEmployees());
    }
}
