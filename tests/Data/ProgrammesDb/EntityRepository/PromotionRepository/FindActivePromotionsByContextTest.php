<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;

use DateTimeImmutable;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\PromotionRepository::<public>
 */
class FindActivePromotionsByContextTest extends AbstractDatabaseTest
{
    public function testFindActivePromotionsByContextFetchAllActive()
    {
        $this->loadFixtures(['PromotionsFixture']);
        $promotionRepository = $this->getRepository('ProgrammesPagesService:Promotion');

        $dbPromotions = $promotionRepository->findActivePromotionsByContext(1, new DateTimeImmutable(), 300, 0);

        $this->assertEquals(['p000000h', 'p000001h', 'p000004h'], array_column($dbPromotions, 'pid'));
        $this->assertEquals(
            ['series', 'brand'],
            $this->getParentTypesRecursively($dbPromotions[0]['promotionOfCoreEntity'])
        );

        $this->assertEquals(
            ['episode', 'series', 'brand'],
            $this->getParentTypesRecursively($dbPromotions[2]['promotionOfCoreEntity'])
        );

        $this->assertCount(2, $this->getDbQueries());
    }

    public function testFindActivePromotionsByContextNoFetchSuperPromotions()
    {
        $this->loadFixtures(['PromotionsFixture']);
        $promotionRepository = $this->getRepository('ProgrammesPagesService:Promotion');

        $dbPromotions = $promotionRepository->findActivePromotionsByContext(1, new DateTimeImmutable(), 300, 0, false);

        $this->assertEquals(['p000000h', 'p000001h'], array_column($dbPromotions, 'pid'));
        $this->assertEquals(
            ['series', 'brand'],
            $this->getParentTypesRecursively($dbPromotions[0]['promotionOfCoreEntity'])
        );

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
