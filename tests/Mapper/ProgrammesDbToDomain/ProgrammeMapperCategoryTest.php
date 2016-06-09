<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ProgrammeMapper;

class ProgrammeMapperCategoryTest extends BaseProgrammeMapperTestCase
{
    public function testGetDomainModelSeriesWithSetGenresAndFormats()
    {
        $genreDbEntity = ['type' => 'genre'];
        $formatDbEntity = ['type' => 'format'];

        $expectedGenreDomainEntity = $this->getMockBuilder(
            'BBC\ProgrammesPagesService\Domain\Entity\Genre'
        )
            ->disableOriginalConstructor()
            ->getMock();

        $expectedFormatDomainEntity = $this->getMockBuilder(
            'BBC\ProgrammesPagesService\Domain\Entity\Format'
        )
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockCategoryMapper->expects($this->exactly(2))
            ->method('getDomainModel')
            ->withConsecutive(
                [$genreDbEntity],
                [$formatDbEntity]
            )
            ->will($this->onConsecutiveCalls(
                $expectedGenreDomainEntity,
                $expectedFormatDomainEntity
            ));

        $dbEntityArray = $this->getSampleProgrammeDbEntity(
            'b010t19z',
            null,
            null,
            [$genreDbEntity, $formatDbEntity]
        );

        $expectedEntity = $this->getSampleProgrammeDomainEntity(
            'b010t19z',
            $this->mockDefaultImage,
            null,
            [$expectedGenreDomainEntity],
            [$expectedFormatDomainEntity]
        );

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($dbEntityArray));
    }
}
