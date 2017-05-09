<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ServiceRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ServiceRepository;
use BBC\ProgrammesPagesService\Domain\ValueObject\Nid;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ServiceRepository::<public>
 */
class FindAllInNetworkTest extends AbstractDatabaseTest
{
    /** @var  ServiceRepository */
    private $repo;

    public function setUp()
    {
        $this->loadFixtures(['NetworksFixture']);
        $this->repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Service');
    }

    public function testFindAllInNetwork()
    {
        $nid = new Nid('bbc_radio_two');
        $servicesInNetwork = $this->repo->findAllInNetwork($nid);

        $this->assertInternalType('array', $servicesInNetwork);
        $this->assertCount(1, $servicesInNetwork);
        $this->assertEquals('p00fzl8v', $servicesInNetwork[0]['pid']);
        $this->assertEquals('National Radio', $servicesInNetwork[0]['type']);

        // must have only been one query (including the join)
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindAllInNetworkWhenEmptyResult()
    {
        $nid = new Nid('unexisting_bbc_radio');
        $servicesInNetwork = $this->repo->findAllInNetwork($nid);
        $this->assertSame([], $servicesInNetwork);

        $this->assertCount(1, $this->getDbQueries());
    }
}
