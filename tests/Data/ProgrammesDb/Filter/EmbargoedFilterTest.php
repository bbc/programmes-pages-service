<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Filter;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Filter\EmbargoedFilter;
use PHPUnit_Framework_TestCase;

class EmbargoedFilterTest extends PHPUnit_Framework_TestCase
{
    private $mockEntityManager;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $mockClassMetadata;

    public function setup()
    {
        $this->mockEntityManager = $this->getMock('Doctrine\ORM\EntityManagerInterface');

        $this->mockClassMetadata = $this->getMockWithoutInvokingTheOriginalConstructor(
            'Doctrine\ORM\Mapping\ClassMetadata'
        );
    }

    public function testEmbargoableItem()
    {
        $this->mockClassMetadata
            ->expects($this->any())
            ->method('hasField')
            ->with($this->equalTo('isEmbargoed'))
            ->willReturn(true);

        $this->mockClassMetadata
            ->expects($this->any())
            ->method('getColumnName')
            ->with($this->equalTo('isEmbargoed'))
            ->willReturn('isEmbargo');

        $filter = new EmbargoedFilter($this->mockEntityManager);

        $this->assertEquals('table.isEmbargo = 0', $filter->addFilterConstraint($this->mockClassMetadata, 'table'));
    }

    public function testNotEmbargoableItem()
    {
        $this->mockClassMetadata
            ->expects($this->any())
            ->method('hasField')
            ->with($this->equalTo('isEmbargoed'))
            ->willReturn(false);

        $filter = new EmbargoedFilter($this->mockEntityManager);

        $this->assertEquals('', $filter->addFilterConstraint($this->mockClassMetadata, 'table'));
    }
}
