<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\PromotionRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\PromotionRepository;
use DateTimeImmutable;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\PromotionRepository::<public>
 */
class FindActivePromotionsByContextTest extends AbstractDatabaseTest
{
    /** @var PromotionRepository */
    private $promotionRepository;

    public function setUp()
    {
        parent::setUp();

        $this->loadFixtures(['PromotionsFixture']);
        $this->promotionRepository = $this->getRepository('ProgrammesPagesService:Promotion');
    }

    public function testActiveSuperpromotionsAreReceivedWithSuperPromotionsForBrand()
    {
        $brandDbAncestryIds = $this->getAncestryFromPersistentIdentifier('b010t19z', 'CoreEntity');
        $dbPromotions = $this->promotionRepository->findActivePromotionsByContext($brandDbAncestryIds, new DateTimeImmutable(), 300, 0);

        // only promotions received by the current context (brand) are received in the right order
        $this->assertEquals(['p000001h', 'p000000h'], array_column($dbPromotions, 'pid'));
        $this->assertEquals([3, 5], array_column($dbPromotions, 'weighting'));
        $this->assertEquals(
            ['episode', 'series', 'brand'],
            $this->getParentTypesRecursively($dbPromotions[0]['promotionOfCoreEntity'])
        );

        $this->assertCount(2, $this->getDbQueries());
    }

    public function testActiveSuperpromotionsAreReceivedWithSuperPromotionsForSeries()
    {
        $seriesDbAncestryIds = $this->getAncestryFromPersistentIdentifier('b00swyx1', 'CoreEntity');
        $dbPromotions = $this->promotionRepository->findActivePromotionsByContext($seriesDbAncestryIds, new DateTimeImmutable(), 300, 0);

        // it fetchs promotion p000001h (super promotion placed in this context)
        $this->assertEquals(['p000003h', 'p000002h'], array_column($dbPromotions, 'pid'));
        $this->assertEquals([2, 4], array_column($dbPromotions, 'weighting'));
        $this->assertEquals(
            ['episode', 'series', 'brand'],
            $this->getParentTypesRecursively($dbPromotions[0]['promotionOfCoreEntity'])
        );

        $this->assertCount(2, $this->getDbQueries());
    }

    public function testActiveSuperpromotionsAreReceivedWithSuperPromotionsForEpisode()
    {
        $episodeDbAncestryIds = $this->getAncestryFromPersistentIdentifier('b00syxx6', 'CoreEntity');
        $dbPromotions = $this->promotionRepository->findActivePromotionsByContext($episodeDbAncestryIds, new DateTimeImmutable(), 300, 0);

        // it fetch promotions in this order: [current context by weight + parent context by weight]
        $this->assertEquals(['p000007h', 'p000006h', 'p000003h', 'p000002h'], array_column($dbPromotions, 'pid'));
        $this->assertEquals([3, 3, 2, 4], array_column($dbPromotions, 'weighting'));
        $this->assertEquals(
            ['episode', 'series', 'brand'],
            $this->getParentTypesRecursively($dbPromotions[0]['promotionOfCoreEntity'])
        );

        $this->assertCount(2, $this->getDbQueries());
    }

    public function testActiveSuperpromotionsAreReceivedWithSuperPromotionsForImage()
    {
        $seriesdDbAncestryIds = $this->getAncestryFromPersistentIdentifier('b010t150', 'CoreEntity');
        $dbPromotions = $this->promotionRepository->findActivePromotionsByContext($seriesdDbAncestryIds, new DateTimeImmutable(), 300, 0);

        // we fetch the only promotion of image in this context. No super propmotions are inherited in this context.
        $this->assertEquals(['p000009h'], array_column($dbPromotions, 'pid'));
        $this->assertEquals('standard', $dbPromotions[0]['promotionOfImage']['type']);

        $this->assertCount(1, $this->getDbQueries());
    }

    private function getParentTypesRecursively($parent, $types = [])
    {
        if (!empty($parent)) {
            $types[] = $parent['type'];
            if (isset($parent['parent'])) {
                return $this->getParentTypesRecursively($parent['parent'], $types);
            }

            return $types;
        }
    }
}
