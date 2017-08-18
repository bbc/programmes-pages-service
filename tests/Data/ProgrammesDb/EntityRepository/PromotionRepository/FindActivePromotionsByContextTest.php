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

        // only the promotion promoted by the brand is received
        $this->assertEquals(['p000000h'], array_column($dbPromotions, 'pid'));
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
        $this->assertEquals(['p000001h'], array_column($dbPromotions, 'pid'));
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

        // it fetchs promotion p000001h (super promotion inherited from series) and the p000004h (regular promotions in this context)
        $this->assertEquals(['p000001h', 'p000004h'], array_column($dbPromotions, 'pid'));
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
        $this->assertEquals(['p000005h'], array_column($dbPromotions, 'pid'));
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
