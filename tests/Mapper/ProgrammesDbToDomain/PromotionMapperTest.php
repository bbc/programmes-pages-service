<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Promotion;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\PromotionMapper;

class PromotionMapperTest extends BaseMapperTestCase
{
    protected $mockMappedImage;

    protected $mockMappedCoreEntity;

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
            'id' => 2,
            'pid' => 'p000000h',
            'weighting' => 73,
            'title' => 'active promotion of CoreEntity',
            'uri' => 'www.myuri.com',
            'shortSynopsis' => 'a short synopsis',
            'mediumSynopsis' => 'a medium synopsis',
            'longSynopsis' => 'a long synopsys',
            'promotionOfCoreEntity' => null,
            'promotionOfImage' => [
                'some_key' => 'some_content',
            ],
            'cascadesToDescendants' => false,
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
            73,
            false
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
            'weighting' => 73,
            'title' => 'active promotion of CoreEntity',
            'uri' => 'www.myuri.com',
            'shortSynopsis' => 'a short synopsis',
            'mediumSynopsis' => 'a medium synopsis',
            'longSynopsis' => 'a long synopsys',
            'promotionOfCoreEntity' => [
                'some_key' => 'some_content',
            ],
            'promotionOfImage' => null,
            'cascadesToDescendants' => true,
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
            73,
            true
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

    /**
    * @expectedException BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
    */
    public function testPromotionWithNoFetchedDataThrowException()
    {
        $promotionMapper = $this->getPromotionMapper();
        $dbPromotionOfImage = [
            'id' => 1,
            'pid' => 'p000000h',
            'weighting' => 73,
            'title' => 'active promotion of CoreEntity',
            'uri' => 'www.myuri.com',
            'shortSynopsis' => 'a short synopsis',
            'mediumSynopsis' => 'a medium synopsis',
            'longSynopsis' => 'a long synopsys',
            'promotionOfCoreEntity' => null,
            'promotionOfImage' => null,
            'cascadesToDescendants' => false,
        ];

        $promotionMapper->getDomainModel($dbPromotionOfImage);
    }

    /**
     * @expectedException BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     */
    public function testPromotionWithNoFetchedDataThrowExceptionWhenNotJoined()
    {
        $promotionMapper = $this->getPromotionMapper();
        $dbPromotionOfImage = [
            'id' => 1,
            'pid' => 'p000000h',
            'weighting' => 73,
            'title' => 'active promotion of CoreEntity',
            'uri' => 'www.myuri.com',
            'shortSynopsis' => 'a short synopsis',
            'mediumSynopsis' => 'a medium synopsis',
            'longSynopsis' => 'a long synopsys',
            'cascadesToDescendants' => false,
        ];

        $promotionMapper->getDomainModel($dbPromotionOfImage);
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
