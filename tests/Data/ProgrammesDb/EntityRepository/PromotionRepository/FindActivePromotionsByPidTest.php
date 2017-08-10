<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use DateTimeImmutable;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\PromotionRepository::<public>
 */
class FindActivePromotionsByPidTest extends AbstractDatabaseTest
{
    public function testActivePromotionsAreReceived()
    {
        $this->loadFixtures(['PromotionsFixture']);
        $promotionRepository = $this->getRepository('ProgrammesPagesService:Promotion');

        $brandPid = new Pid('b010t19z');
        $dbPromotions = $promotionRepository->findActivePromotionsByPid($brandPid, new DateTimeImmutable(), 300, 0);

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
