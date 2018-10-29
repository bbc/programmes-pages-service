<?php
declare(strict_types = 1);

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\VersionRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\VersionRepository;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\VersionRepository::<public>
 */
class CompetitionWarningsTest extends AbstractDatabaseTest
{
    public function setUp()
    {
        $this->enableEmbargoedFilter();
    }

    public function tearDown()
    {
        $this->disableEmbargoedFilter();
    }

    public function testFindAllStreamableByProgrammeItemReturnsCompetitionWarningsWhenProgrammeHasMasterbrand()
    {
        $this->loadFixtures(['CompetitionWarningFixture']);

        /** @var VersionRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Version');

        $programmeDbId = $this->getCoreEntityDbId('p0000012');

        $list = $repo->findAllStreamableByProgrammeItem((string) $programmeDbId);
        $this->assertCount(2, $list);
        $this->assertEquals('v0000018', $list[0]['pid']);
        $this->assertEquals('v0000021', $list[1]['pid']);

        $this->assertEquals(
            'Radio Four Competition Warning',
            $list[0]['programmeItem']['masterBrand']['competitionWarning']['programmeItem']['title']
        );

        $this->assertEquals(
            'Radio Four Competition Warning',
            $list[1]['programmeItem']['masterBrand']['competitionWarning']['programmeItem']['title']
        );

        // must have only been one query (including the join)
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindAllStreamableByProgrammeItemReturnsCompetitionWarningsWhenProgrammeParentHasMasterbrand()
    {
        $this->loadFixtures(['CompetitionWarningFixture']);

        /** @var VersionRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Version');

        $programmeDbId = $this->getCoreEntityDbId('p0000015');

        $list = $repo->findAllStreamableByProgrammeItem((string) $programmeDbId);
        $this->assertCount(2, $list);
        $this->assertEquals('v0000019', $list[0]['pid']);
        $this->assertEquals('v0000022', $list[1]['pid']);

        $this->assertEquals(
            'Radio Four Competition Warning',
            $list[0]['programmeItem']['parent']['masterBrand']['competitionWarning']['programmeItem']['title']
        );

        $this->assertEquals(
            'Radio Four Competition Warning',
            $list[1]['programmeItem']['parent']['masterBrand']['competitionWarning']['programmeItem']['title']
        );

        $this->assertCount(2, $this->getDbQueries());
    }

    public function testFindLinkedVersionsForProgrammeItemReturnsCompetitionWarningsWhenProgrammeHasMasterbrand()
    {
        $this->loadFixtures(['CompetitionWarningFixture']);

        /** @var VersionRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Version');

        $programmeDbId = $this->getCoreEntityDbId('p0000012');

        $programmeItem = $repo->findLinkedVersionsForProgrammeItem((string) $programmeDbId);
        $this->assertEquals('v0000018', $programmeItem['streamableVersion']['pid']);

        $this->assertEquals(
            'Radio Four Competition Warning',
            $programmeItem['streamableVersion']['programmeItem']['masterBrand']['competitionWarning']['programmeItem']['title']
        );

        // must have only been one query (including the join)
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindLinkedVersionsForProgrammeItemReturnsCompetitionWarningsWhenProgrammeParentHasMasterbrand()
    {
        $this->loadFixtures(['CompetitionWarningFixture']);

        /** @var VersionRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Version');

        $programmeDbId = $this->getCoreEntityDbId('p0000015');

        $programmeItem = $repo->findLinkedVersionsForProgrammeItem((string) $programmeDbId);
        $this->assertEquals('v0000019', $programmeItem['streamableVersion']['pid']);
        $this->assertEquals('p0000015', $programmeItem['pid']);

        $this->assertEquals(
            'Radio Four Competition Warning',
            $programmeItem['streamableVersion']['programmeItem']['parent']['masterBrand']['competitionWarning']['programmeItem']['title']
        );

        $this->assertCount(2, $this->getDbQueries());
    }

    public function testFindLinkedVersionsForProgrammeItemReturnsCorrectlyWhenNotStreamable()
    {
        $this->loadFixtures(['CompetitionWarningFixture']);

        /** @var VersionRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Version');

        $programmeDbId = $this->getCoreEntityDbId('p0000016');

        $programmeItem = $repo->findLinkedVersionsForProgrammeItem((string) $programmeDbId);
        $this->assertEquals('p0000016', $programmeItem['pid']);

        $this->assertFalse(isset($programmeItem['streamableVersion']));

        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindLinkedVersionsForProgrammeItemReturnsCorrectlyWhenNoProgramme()
    {
        $this->loadFixtures(['CompetitionWarningFixture']);

        /** @var VersionRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Version');

        $programmeDbId = 999999999999;

        $programmeItem = $repo->findLinkedVersionsForProgrammeItem((string) $programmeDbId);

        $this->assertNull($programmeItem);
    }

    public function testFindStreamableVersionForProgrammeItems()
    {
        $this->loadFixtures(['CompetitionWarningFixture']);

        /** @var VersionRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Version');

        $programmeDbIds = [
            $this->getCoreEntityDbId('p0000016'),
            $this->getCoreEntityDbId('p0000011'),
            $this->getCoreEntityDbId('p0000012'),
            $this->getCoreEntityDbId('p0000015'),
        ];

        $programmeItems = $repo->findStreamableVersionForProgrammeItems($programmeDbIds);
        // We don't care about ordering in the response
        usort($programmeItems, function ($a, $b) {
            return $a['pid'] <=> $b['pid'];
        });
        $this->assertCount(3, $programmeItems);
        $this->assertEquals('p0000011', $programmeItems[0]['pid']);
        $this->assertEquals('v0000017', $programmeItems[0]['streamableVersion']['pid']);
        $this->assertFalse(isset($programmeItems[0]['streamableVersion']['programmeItem']['masterBrand']['competitionWarning']));
        $this->assertEquals('p0000012', $programmeItems[1]['pid']);
        $this->assertEquals('v0000018', $programmeItems[1]['streamableVersion']['pid']);
        $this->assertEquals(
            'Radio Four Competition Warning',
            $programmeItems[1]['streamableVersion']['programmeItem']['masterBrand']['competitionWarning']['programmeItem']['title']
        );
        $this->assertEquals('p0000015', $programmeItems[2]['pid']);
        $this->assertEquals('v0000019', $programmeItems[2]['streamableVersion']['pid']);
        $this->assertEquals(
            'Radio Four Competition Warning',
            $programmeItems[2]['streamableVersion']['programmeItem']['parent']['masterBrand']['competitionWarning']['programmeItem']['title']
        );
    }
}
