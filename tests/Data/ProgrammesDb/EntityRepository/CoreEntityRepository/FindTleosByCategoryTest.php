<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository::<public>
 */
class FindTleosByCategoryTest extends AbstractDatabaseTest
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

    public function testFindTleosByCategoryAllAvailabilityInCategory()
    {
        /** @var CoreEntityRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        $selectedCategoryForTleos = [1, 2, 3]; // /cat1/cat11
        $selectedAvailabilityForTleos = false;

        $tleos = $repo->findTleosByCategory(
            $selectedCategoryForTleos,
            $selectedAvailabilityForTleos,
            null,
            0
        );

        $this->assertInternalType('array', $tleos);
        $this->assertCount(1, $tleos);

        $this->assertEquals('Brand2', $tleos[0]['title']);
    }

    public function testFindTleosByCategoryAllAvailability()
    {
        /** @var CoreEntityRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        $selectedCategoryForTleos = [1, 2]; // /cat1/cat11
        $selectSpecificAvailavility = false;

        $tleos = $repo->findTleosByCategory(
            $selectedCategoryForTleos,
            $selectSpecificAvailavility,
            null,
            0
        );

        $this->assertInternalType('array', $tleos);
        $this->assertCount(2, $tleos);

        $this->assertEquals('Brand1', $tleos[0]['title']);
        $this->assertEquals('Brand2', $tleos[1]['title']);
    }

    public function testFindTleosByCategoryPlayerAvailability()
    {
        /** @var CoreEntityRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        $selectedCategoryForTleos = [1, 2]; // /cat1/cat11
        $selectedAvailabilityForTleos = true;

        $tleos = $repo->findTleosByCategory(
            $selectedCategoryForTleos,
            $selectedAvailabilityForTleos,
            null,
            0
        );

        $this->assertInternalType('array', $tleos);
        $this->assertCount(1, $tleos);

        $this->assertEquals('Brand1', $tleos[0]['title']);
    }
}
