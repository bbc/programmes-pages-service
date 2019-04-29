<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository::<public>
 */
class CountTleosByCategoryTest extends AbstractDatabaseTest
{
    public function setUp()
    {
        /**
         * From fixture:
         *
         *  brand1
         *      cat1/cat11          (category) - /1/2
         *      form1               (format) - /4
         *      streamable=True     (availability)
         *
         *  brand2
         *      cat1/cat11/cat111   (category) - /1/2/3
         *      form2               (format) - /5
         *      streamable=False    (availability)
         */
        $this->loadFixtures(['TleosByCategoryFixture']);
    }

    public function tearDown()
    {
        $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity')->clearAncestryCache();
    }

    public function testCountTleosByCategoryAll()
    {
        /** @var CoreEntityRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');
        $tleos = $repo->countTleosByCategories([1, 2], false);
        $this->assertSame(2, $tleos);
    }

    public function testCountTleosByCategoryStreamable()
    {
        /** @var CoreEntityRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');
        $tleos = $repo->countTleosByCategories([1, 2], true);
        $this->assertEquals(1, $tleos);
    }

    public function testCountTleosByCategoryNonexistentCategory()
    {
        /** @var CoreEntityRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');
        $tleos = $repo->countTleosByCategories([99], false);
        $this->assertEquals(0, $tleos);
    }
}
