<?php
declare(strict_types=1);

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ServiceRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ServiceRepository;
use BBC\ProgrammesPagesService\Domain\ValueObject\Sid;
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
        $sid = new Sid('bbc_radio_fourlw');
        $dbId = $this->getDbId($sid);
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

    protected function getDbId($sid)
    {
        // Disable the logger for this call as we don't want to count it
        $this->getEntityManager()->getConfiguration()->getSQLLogger()->enabled = false;

        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Service');
        $id = $repo->findOneBySid($sid)->getId();

        // Re enable the SQL logger
        $this->getEntityManager()->getConfiguration()->getSQLLogger()->enabled = true;

        return $id;
    }
}
