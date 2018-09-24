<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\VersionRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\VersionRepository;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers \BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\VersionRepository::<public>
 */
class FindAlternateVersionsForProgrammeItemTest extends AbstractDatabaseTest
{
    public function setUp()
    {
        $this->enableEmbargoedFilter();
    }

    public function tearDown()
    {
        $this->disableEmbargoedFilter();
    }

    public function testOneAlternateVersion()
    {
        $this->loadFixtures(['VersionFixture']);
        /** @var VersionRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Version');

        $programmeDbId = (string) $this->getCoreEntityDbId('p0000007');

        $list = $repo->findAlternateVersionsForProgrammeItem($programmeDbId);
        $this->assertCount(1, $list);
        $this->assertEquals('v0000011', $list[0]['pid']);

        // must have only been one query (including the join)
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testMultipleAlternateVersions()
    {
        $this->loadFixtures(['VersionFixture']);
        /** @var VersionRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Version');

        $programmeDbId = (string) $this->getCoreEntityDbId('p0000008');

        $list = $repo->findAlternateVersionsForProgrammeItem($programmeDbId);
        $this->assertCount(2, $list);
        $this->assertEquals('v0000012', $list[0]['pid']);
        $this->assertEquals('v0000013', $list[1]['pid']);

        // must have only been one query (including the join)
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testNoAlternateVersions()
    {
        $this->loadFixtures(['VersionFixture']);
        /** @var VersionRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Version');

        $programmeDbId = (string) $this->getCoreEntityDbId('p0000001');

        $list = $repo->findAlternateVersionsForProgrammeItem($programmeDbId);
        $this->assertCount(0, $list);

        // must have only been one query (including the join)
        $this->assertCount(1, $this->getDbQueries());
    }
}
