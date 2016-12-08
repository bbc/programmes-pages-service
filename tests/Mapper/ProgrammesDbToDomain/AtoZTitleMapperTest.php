<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\AtoZTitleMapper;
use BBC\ProgrammesPagesService\Domain\Entity\AtoZTitle;

class AtoZTitleMapperTest extends BaseMapperTestCase
{
    protected $mockProgrammeMapper;

    public function setUp()
    {
        $this->mockProgrammeMapper = $this->createMock(
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ProgrammeMapper'
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

        $expectedEntity = new AtoZTitle('Title', 'T', $expectedProgrammeDomainEntity);

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($dbEntityArray));
    }

    /**
     * @expectedException \BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     * @expectedExceptionMessage All AtoZTitles must be joined to a CoreEntity
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
            'ProgrammeMapper' => $this->mockProgrammeMapper,
        ]));
    }
}
