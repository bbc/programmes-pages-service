<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;

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
        $coreEntityRepo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        $brand = $coreEntityRepo->findByPid('b010t19z', 'Brand');
        $brandDbAncestryIds = array_filter(explode(',', $brand['ancestry']));

        $dbPromotions = $this->promotionRepository->findActivePromotionsByContext($brandDbAncestryIds, new DateTimeImmutable(), 300, 0);

        // only the promotion promoted by the brand is received
        $this->assertEquals(['p000000h'], array_column($dbPromotions, 'pid'));
        $this->assertEquals(
            ['episode', 'series', 'brand'],
            $this->getParentTypesRecursively($dbPromotions[0]['promotionOfCoreEntity'])
        );

        $this->assertCount(3, $this->getDbQueries());
    }

    public function testActiveSuperpromotionsAreReceivedWithSuperPromotionsForSeries()
    {
        $coreEntityRepo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        $series = $coreEntityRepo->findByPid('b00swyx1', 'Series'); // series 1/episode 1
        $seriesDbAncestryIds = array_filter(explode(',', $series['ancestry']));

        $dbPromotions = $this->promotionRepository->findActivePromotionsByContext($seriesDbAncestryIds, new DateTimeImmutable(), 300, 0);

        // it fetchs promotion p000001h (super promotion placed in this context)
        $this->assertEquals(['p000001h'], array_column($dbPromotions, 'pid'));
        $this->assertEquals(
            ['episode', 'series', 'brand'],
            $this->getParentTypesRecursively($dbPromotions[0]['promotionOfCoreEntity'])
        );

        $this->assertCount(3, $this->getDbQueries());
    }


    public function testActiveSuperpromotionsAreReceivedWithSuperPromotionsForEpisode()
    {
        $coreEntityRepo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        $series = $coreEntityRepo->findByPid('b00syxx6', 'Episode'); // series 1/episode 1
        $seriesDbAncestryIds = array_filter(explode(',', $series['ancestry']));

        $dbPromotions = $this->promotionRepository->findActivePromotionsByContext($seriesDbAncestryIds, new DateTimeImmutable(), 300, 0);

        // it fetchs promotion p000001h (super promotion inherited from series) and the p000004h (regular promotions in this context)
        $this->assertEquals(['p000001h', 'p000004h'], array_column($dbPromotions, 'pid'));
        $this->assertEquals(
            ['episode', 'series', 'brand'],
            $this->getParentTypesRecursively($dbPromotions[0]['promotionOfCoreEntity'])
        );

        $this->assertCount(3, $this->getDbQueries());
    }

    public function testActiveSuperpromotionsAreReceivedWithSuperPromotionsForImage()
    {
        $coreEntityRepo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        $brand = $coreEntityRepo->findByPid('b010t150', 'Series'); // brand 1/series 2
        $brandDbAncestryIds = array_filter(explode(',', $brand['ancestry']));

        $dbPromotions = $this->promotionRepository->findActivePromotionsByContext($brandDbAncestryIds, new DateTimeImmutable(), 300, 0);

        // we fetch the only promotion of image in this context. No super propmotions are inherited in this context.
        $this->assertEquals(['p000005h'], array_column($dbPromotions, 'pid'));
        $this->assertEquals('standard', $dbPromotions[0]['promotionOfImage']['type']);

        $this->assertCount(2, $this->getDbQueries());
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
