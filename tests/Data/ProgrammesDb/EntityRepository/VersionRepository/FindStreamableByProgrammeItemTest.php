<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\VersionRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers \BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\VersionRepository::<public>
 */
class FindStreamableByProgrammeItemTest extends AbstractDatabaseTest
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
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Version');

        $programmeDbId = $this->getCoreEntityDbId('p0000001');

        $list = $repo->findByProgrammeItem($programmeDbId);
        $this->assertCount(3, $list);
        $this->assertEquals('v0000001', $list[0]['pid']);
        $this->assertEquals('v0000003', $list[1]['pid']);
        $this->assertEquals('v0000004', $list[2]['pid']);

        // must have only been one query (including the join)
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindStreamableByProgrammeItemWhenEmptyResult()
    {
        $this->loadFixtures(['VersionFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Version');

        $list = $repo->findByProgrammeItem(999);
        $this->assertSame([], $list);

        // findByProgrammeItem query only
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindStreamableByProgrammeItemWhenParentIsEmbargoed()
    {
        $this->loadFixtures(['VersionFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Version');
        $programmeDbId = $this->getCoreEntityDbId('p0000002');

        $list = $repo->findByProgrammeItem($programmeDbId);
        $this->assertSame([], $list);

        // findByProgrammeItem query only
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindStreamableByProgrammeItemWhenParentIsEmbargoedAndFilterIsDisabled()
    {
        $this->disableEmbargoedFilter();

        $this->loadFixtures(['VersionFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Version');
        $programmeDbId = $this->getCoreEntityDbId('p0000002');

        $list = $repo->findByProgrammeItem($programmeDbId);
        $this->assertCount(1, $list);
        $this->assertEquals('v0000002', $list[0]['pid']);

        // findByPid query only
        $this->assertCount(1, $this->getDbQueries());
    }
}
