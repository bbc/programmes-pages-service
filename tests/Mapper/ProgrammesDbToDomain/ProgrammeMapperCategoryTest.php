<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

class ProgrammeMapperCategoryTest extends BaseProgrammeMapperTestCase
{
    public function testGetDomainModelSeriesWithSetGenresAndFormats()
    {
        $genreDbEntity = ['type' => 'genre'];
        $formatDbEntity = ['type' => 'format'];

        $expectedGenreDomainEntity = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\Genre'
        );

        $expectedFormatDomainEntity = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\Format'
        );

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

    public function testGetDomainModelSeriesWithSetButEmptyGenresAndFormats()
    {
        $genreDbEntity = ['type' => 'genre'];
        $formatDbEntity = ['type' => 'format'];

        $dbEntityArray = $this->getSampleProgrammeDbEntity(
            'b010t19z',
            null,
            null,
            []
        );

        $expectedEntity = $this->getSampleProgrammeDomainEntity(
            'b010t19z',
            $this->mockDefaultImage,
            null,
            [],
            []
        );

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($dbEntityArray));
    }
}
