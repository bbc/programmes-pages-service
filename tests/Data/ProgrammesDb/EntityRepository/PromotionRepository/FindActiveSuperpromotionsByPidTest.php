<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\PromotionRepository;
use DateTimeImmutable;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\PromotionRepository::<public>
 */
class FindActiveSuperpromotionsByPidTest extends AbstractDatabaseTest
{
    /**
     * @var PromotionRepository
     */
    private $promotionRepository;

    public function setUp()
    {
        parent::setUp();

        $this->loadFixtures(['PromotionsFixture']);
        $this->promotionRepository = $this->getRepository('ProgrammesPagesService:Promotion');
    }

    public function testActiveSuperpromotionsAreReceivedACoreEntity()
    {
        $coreEntityRepo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        $series = $coreEntityRepo->findByPid('b00swyx1', 'Series');
        $seriesDbAncestryIds = explode(',', $series['ancestry']);

        $dbPromotions = $this->promotionRepository->findActiveSuperPromotionsByAncestry($seriesDbAncestryIds, new DateTimeImmutable(), 300, 0);

        $this->assertEquals(['p000004h'], array_column($dbPromotions, 'pid'));
        $this->assertEquals(
            ['episode', 'series', 'brand'],
            $this->getParentTypesRecursively($dbPromotions[0]['promotionOfCoreEntity'])
        );

        $this->assertCount(3, $this->getDbQueries());
    }

    public function testActiveSuperpromotionsAreReceivedWhenBrandContext()
    {
        $coreEntityRepo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        $brand = $coreEntityRepo->findByPid('b010t19z', 'Brand');
        $brandDbAncestryIds = explode(',', $brand['ancestry']);

        $dbPromotions = $this->promotionRepository->findActiveSuperPromotionsByAncestry($brandDbAncestryIds, new DateTimeImmutable(), 300, 0);

        $this->assertEquals(['p000004h'], array_column($dbPromotions, 'pid'));
        $this->assertEquals(
            ['episode', 'series', 'brand'],
            $this->getParentTypesRecursively($dbPromotions[0]['promotionOfCoreEntity'])
        );

        $this->assertCount(3, $this->getDbQueries());
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
