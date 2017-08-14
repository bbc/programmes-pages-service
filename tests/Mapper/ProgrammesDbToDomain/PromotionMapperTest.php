<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Promotion;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\PromotionMapper;
use DateTimeImmutable;

class PromotionMapperTest extends BaseMapperTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockMappedImage = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Image');
        $this->mockMappedCoreEntity = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Clip');
    }

    public function testMapperConvertFromDbDataToPromotionWithImage()
    {
        $promotionMapper = $this->getPromotionMapper();

        $dbPromotionOfImage = [
            'id' => 1,
            'pid' => 'p000000h',
            'startDate' => new DateTimeImmutable('1900-01-01 00:00:00.000000'),
            'endDate' => new DateTimeImmutable('3000-01-01 00:00:00.000000'),
            'weighting' => 73,
            'isActive' => 1,
            'promotedFor' => null,
            'title' => 'active promotion of CoreEntity',
            'uri' => 'www.myuri.com',
            'cascadesToDescendants' => false,
            'createdAt' => new DateTimeImmutable('2017-08-11 10:39:25.000000'),
            'updatedAt' => new DateTimeImmutable('2017-08-11 10:39:25.000000'),
            'partnerPid' => 's0000001',
            'shortSynopsis' => 'a short synopsis',
            'mediumSynopsis' => 'a medium synopsis',
            'longSynopsis' => 'a long synopsys',
            'promotionOfCoreEntity' => null,
            'promotionOfImage' => [
                'some_key' => 'some_content',
            ],
        ];

        $expectedMappedPromotion = new Promotion(
            new Pid('p000000h'),
            $this->mockMappedImage,
            'active promotion of CoreEntity',
            new Synopses(
                'a short synopsis',
                'a medium synopsis',
                'a long synopsys'
            ),
            'www.myuri.com',
            73
        );

        $this->assertEquals(
            $expectedMappedPromotion,
            $promotionMapper->getDomainModel($dbPromotionOfImage)
        );

        // Requesting the same entity multiple times reuses a cached instance
        // of the entity, rather than creating a new one every time
        $this->assertSame(
            $promotionMapper->getDomainModel($dbPromotionOfImage),
            $promotionMapper->getDomainModel($dbPromotionOfImage)
        );
    }

    public function testMapperConvertFromDbDataToPromotionWithCoreEntity()
    {
        $promotionMapper = $this->getPromotionMapper();

        $dbPromotionOfImage = [
            'id' => 1,
            'pid' => 'p000000h',
            'startDate' => new DateTimeImmutable('1900-01-01 00:00:00.000000'),
            'endDate' => new DateTimeImmutable('3000-01-01 00:00:00.000000'),
            'weighting' => 73,
            'isActive' => 1,
            'promotedFor' => null,
            'title' => 'active promotion of CoreEntity',
            'uri' => 'www.myuri.com',
            'cascadesToDescendants' => false,
            'createdAt' => new DateTimeImmutable('2017-08-11 10:39:25.000000'),
            'updatedAt' => new DateTimeImmutable('2017-08-11 10:39:25.000000'),
            'partnerPid' => 's0000001',
            'shortSynopsis' => 'a short synopsis',
            'mediumSynopsis' => 'a medium synopsis',
            'longSynopsis' => 'a long synopsys',
            'promotionOfCoreEntity' => [
                'some_key' => 'some_content',
            ],
            'promotionOfImage' => null,
        ];

        $expectedMappedPromotion = new Promotion(
            new Pid('p000000h'),
            $this->mockMappedCoreEntity,
            'active promotion of CoreEntity',
            new Synopses(
                'a short synopsis',
                'a medium synopsis',
                'a long synopsys'
            ),
            'www.myuri.com',
            73
        );

        $this->assertEquals(
            $expectedMappedPromotion,
            $promotionMapper->getDomainModel($dbPromotionOfImage)
        );

        // Requesting the same entity multiple times reuses a cached instance
        // of the entity, rather than creating a new one every time
        $this->assertSame(
            $promotionMapper->getDomainModel($dbPromotionOfImage),
            $promotionMapper->getDomainModel($dbPromotionOfImage)
        );
    }

    private function getPromotionMapper(): PromotionMapper
    {
        $mockImageMapper = $this->createMock('BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ImageMapper');
        $mockImageMapper->method('getDomainModel')->willReturn($this->mockMappedImage);

        $mockCoreEntityMapper = $this->createMock('BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\CoreEntityMapper');
        $mockCoreEntityMapper->method('getDomainModel')->willReturn($this->mockMappedCoreEntity);

        return new PromotionMapper($this->getMapperFactory([
             'ImageMapper' => $mockImageMapper,
             'CoreEntityMapper' => $mockCoreEntityMapper,
        ]));
    }
}
