<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\VersionRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\VersionRepository;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

class FindAllStreamableByProgrammeItemTest extends AbstractDatabaseTest
{
    public function setUp()
    {
        $this->enableEmbargoedFilter();
    }

    public function tearDown()
    {
        $this->disableEmbargoedFilter();
    }

    public function testFindStreamableByProgrammeItem()
    {
        $this->loadFixtures(['VersionFixture']);
        /** @var VersionRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Version');

        $programmeDbId = $this->getCoreEntityDbId('p0000007');

        $list = $repo->findAllStreamableByProgrammeItem((string) $programmeDbId);
        $this->assertCount(2, $list);
        $this->assertEquals('v0000010', $list[0]['pid']);
        $this->assertEquals('v0000009', $list[1]['pid']);

        // must have only been one query (including the join)
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindStreamableByProgrammeItemWhenEmptyResult()
    {
        $this->loadFixtures(['VersionFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Version');

        $list = $repo->findAllStreamableByProgrammeItem(999);
        $this->assertSame([], $list);

        // findByProgrammeItem query only
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindStreamableByProgrammeItemWhenParentIsEmbargoed()
    {
        $this->loadFixtures(['VersionFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Version');
        $programmeDbId = $this->getCoreEntityDbId('p0000002');

        $list = $repo->findAllStreamableByProgrammeItem($programmeDbId);
        $this->assertSame([], $list);

        // findByProgrammeItem query only
        $this->assertCount(1, $this->getDbQueries());
    }
}
