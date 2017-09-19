<?php
declare(strict_types=1);

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ServiceRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ServiceRepository;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ServiceRepository::<public>
 */
class FindByIdsWithNetworkServicesListTest extends AbstractDatabaseTest
{
    /** @var ServiceRepository */
    private $repo;

    public function setUp()
    {
        $this->loadFixtures(['NetworksFixture']);
        $this->repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Service');
    }

    public function testFindServiceBy()
    {
        $dbId = $this->getDbIdFromPersistentIdentifier('p00fzl7k', 'Service');
        $service = $this->repo->findByIdsWithNetworkServicesList([$dbId]);

        $this->assertInternalType('array', $service);
        $this->assertCount(1, $service);
        $this->assertEquals('p00fzl7k', $service[0]['pid']);
        $this->assertEquals('National Radio', $service[0]['type']);
        $this->assertNotNull($service[0]['network']);
        $this->assertCount(2, $service[0]['network']['services']);

        // must have only been one query (including the join)
        $this->assertCount(1, $this->getDbQueries());
    }
}
