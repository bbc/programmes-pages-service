<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @coversDefaultClass BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\PromotionRepository
 */
class FindPromotionsByPidTest extends AbstractDatabaseTest
{
    private $promotionRepository;

    public function setUp()
    {
        parent::setUp();

        $this->loadFixtures(['PromotionsFixture']);
        $this->promotionRepository = $this->getRepository('ProgrammesPagesService:Promotion');
    }

    /**
     * @covers ::findActivePromotionsByPid
     */
    public function testOnlyActivePromotionsAreReceived()
    {
        $brandPid = new Pid('b010t19z');
        $dbPromotions = $this->promotionRepository->findActivePromotionsByPid($brandPid, 300, 0);

        $this->assertEquals(['p000000h', 'p000001h', 'p000004h'], array_column($dbPromotions, 'pid'));
        $this->assertCount(1, $this->getDbQueries());
    }

    /**
     * @covers ::findActiveSuperPromotionsByPid
     */
    public function testOnlyActiveSuperpromotionsAreReceivedACoreEntity()
    {
        $coreEntityRepo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        $serie = $coreEntityRepo->findByPid('b00swyx1', 'Series');
        $serieDbAncestryIds = explode(',', $serie['ancestry']);

        $dbPromotions = $this->promotionRepository->findActiveSuperPromotionsByPid($serieDbAncestryIds, 300, 0);

        $this->assertEquals(['p000004h'], array_column($dbPromotions, 'pid'));
        $this->assertCount(2, $this->getDbQueries());
    }

    /**
     * @covers ::findActiveSuperPromotionsByPid
     */
    public function testOnlyActiveSuperpromotionsAreReceivedWhenBrandContext()
    {
        $coreEntityRepo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        $brand = $coreEntityRepo->findByPid('b010t19z', 'Brand');
        $brandDbAncestryIds = explode(',', $brand['ancestry']);

        $dbPromotions = $this->promotionRepository->findActiveSuperPromotionsByPid($brandDbAncestryIds, 300, 0);

        $this->assertEquals(['p000004h'], array_column($dbPromotions, 'pid'));
        $this->assertCount(2, $this->getDbQueries());
    }
}
