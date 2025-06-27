<?php

namespace App\tests\unit\module\company\domain\entity;

use App\Module\Company\Domain\Entity\ContractType;
use PHPUnit\Framework\TestCase;

class ContractTypeTest extends TestCase
{
    public function testItSetsAndGetsProperties(): void
    {
        $contractType = new ContractType();

        $contractType->setName('Umowa o pracę');
        $contractType->setDescription('Pełny etat');
        $contractType->setActive(true);

        $this->assertSame('Umowa o pracę', $contractType->getName());
        $this->assertSame('Pełny etat', $contractType->getDescription());
        $this->assertTrue($contractType->getActive());
        $this->assertCount(0, $contractType->getEmployees());
    }
}
