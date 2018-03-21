<?php
declare(strict_types=1);

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\SegmentEventRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\SegmentEventRepository;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

class FindByProgrammeItemUsingOriginalVersionTest extends AbstractDatabaseTest
{
    public function setUp()
    {
        $this->loadFixtures(['SegmentEventFixture']);
    }

    public function tearDown()
    {
        $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity')->clearAncestryCache();
    }

    public function testProgrammeItemWithOriginalVersion()
    {
        /** @var SegmentEventRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:SegmentEvent');

        $episodeId = $this->getDbIdFromPersistentIdentifier('p0000004', 'Episode');

        $result = $repo->findByProgrammeItemUsingOriginalVersion($episodeId, null, 0);

        $this->assertCount(2, $result);
        $this->assertEquals('sv000012', $result[0]['pid']);
        $this->assertEquals('sv000013', $result[1]['pid']);
    }

    public function testProgrammeItemWithoutOriginalVersion()
    {
        /** @var SegmentEventRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:SegmentEvent');

        $episodeId = $this->getDbIdFromPersistentIdentifier('p0000003', 'Episode');
        $result = $repo->findByProgrammeItemUsingOriginalVersion($episodeId, null, 0);

        $this->assertCount(0, $result);
    }

    public function testNonExistentProgrammeItemReturnsEmpty()
    {
        /** @var SegmentEventRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:SegmentEvent');
        $result = $repo->findByProgrammeItemUsingOriginalVersion(9999999, null, 0);
        $this->assertCount(0, $result);
    }
}
