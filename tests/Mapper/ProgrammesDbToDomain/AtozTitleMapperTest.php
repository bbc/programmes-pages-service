<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\AtozTitle;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\AtozTitleMapper;

class AtozTitleMapperTest extends BaseMapperTestCase
{
    protected $mockProgrammeMapper;

    public function setUp()
    {
        $this->mockProgrammeMapper = $this->createMock(
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\CoreEntityMapper'
        );
    }

    public function testGetDomainModel()
    {
        $programmeDbEntity = ['pid' => 'p01m5mss'];

        $expectedProgrammeDomainEntity = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\ProgrammeItem'
        );

        $this->mockProgrammeMapper->expects($this->once())
            ->method('getDomainModel')
            ->with($programmeDbEntity)
            ->willReturn($expectedProgrammeDomainEntity);

        $dbEntityArray = [
            'id' => 1,
            'title' => 'Title',
            'firstLetter' => 'T',
            'coreEntity' => $programmeDbEntity,
        ];

        $expectedEntity = new AtozTitle('Title', 'T', $expectedProgrammeDomainEntity);

        $mapper = $this->getMapper();
        $this->assertEquals($expectedEntity, $mapper->getDomainModel($dbEntityArray));

        // Requesting the same entity multiple times reuses a cached instance
        // of the entity, rather than creating a new one every time
        $this->assertSame(
            $mapper->getDomainModel($dbEntityArray),
            $mapper->getDomainModel($dbEntityArray)
        );
    }

    /**
     * @expectedException \BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     * @expectedExceptionMessage All AtozTitles must be joined to a CoreEntity
     */
    public function testGetDomainModelWithNoProgramme()
    {
        $dbEntityArray = [
            'id' => 1,
            'title' => 'Title',
            'firstLetter' => 'T',
        ];

        $this->getMapper()->getDomainModel($dbEntityArray);
    }


    private function getMapper(): AToZTitleMapper
    {
        return new AToZTitleMapper($this->getMapperFactory([
            'CoreEntityMapper' => $this->mockProgrammeMapper,
        ]));
    }
}
