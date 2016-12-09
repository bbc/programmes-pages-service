<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\AtozTitleRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\AtozTitleRepository;
use BBC\ProgrammesPagesService\Domain\Enumeration\NetworkMediumEnum;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

class FindTleosByFirstLetterTest extends AbstractDatabaseTest
{
    /** @var AtozTitleRepository $repo */
    private $repo;

    public function setUp()
    {
        $this->loadFixtures(['AtozTitleFixture']);
        $this->enableEmbargoedFilter();
        $this->repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:AtozTitle');
    }

    public function tearDown()
    {
        $this->disableEmbargoedFilter();
    }

    public function testFindAllLetters()
    {
        $letters = $this->repo->findAllLetters(null);
        $expectedLetters = ['@', 'm', 't', 'w'];
        $this->assertEquals($expectedLetters, $letters);
    }

    public function testFindAllLettersByNetworkMedium()
    {
        $letters = $this->repo->findAllLetters(NetworkMediumEnum::TV);
        $expectedLetters = ['m'];
        $this->assertEquals($expectedLetters, $letters);
    }

    public function testFindByFirstLetterAt()
    {
        $tleos = $this->repo->findTleosByFirstLetter('@', null, false, null, 0);
        $this->assertCount(1, $tleos);
        $this->assertEquals('b0000002', $tleos[0]['coreEntity']['pid']);
    }

    public function testFindByFirstLetterW()
    {
        $tleos = $this->repo->findTleosByFirstLetter('w', null, false, null, 0);
        $this->assertCount(1, $tleos);
        $this->assertEquals('b0000001', $tleos[0]['coreEntity']['pid']);
    }

    public function testGetEmbargoed()
    {
        $tleos = $this->repo->findTleosByFirstLetter('p', null, false, null, 0);
        $this->assertEmpty($tleos);
    }

    public function testFilterAvailable()
    {
        $tleos = $this->repo->findTleosByFirstLetter('w', null, true, null, 0);
        $this->assertEmpty($tleos);

        $tleos = $this->repo->findTleosByFirstLetter('@', null, true, null, 0);
        $this->assertCount(1, $tleos);
        $this->assertEquals('b0000002', $tleos[0]['coreEntity']['pid']);
    }

    public function testFindWithNetworkMedium()
    {
        $tleos = $this->repo->findTleosByFirstLetter('w', NetworkMediumEnum::TV, false, null, 0);
        $this->assertEmpty($tleos);

        $tleos = $this->repo->findTleosByFirstLetter('m', NetworkMediumEnum::TV, false, null, 0);
        $this->assertCount(1, $tleos);
        $this->assertEquals('b010t19z', $tleos[0]['coreEntity']['pid']);
    }

    public function testCountByFirstLetter()
    {
        $count = $this->repo->countTleosByFirstLetter('@', null, false);
        $this->assertEquals(1, $count);
    }

    public function testCountByFirstLetterFilterAvailable()
    {
        $count = $this->repo->countTleosByFirstLetter('w', null, true);
        $this->assertEquals(0, $count);

        $count = $this->repo->countTleosByFirstLetter('@', null, true);
        $this->assertEquals(1, $count);
    }

    public function testCountByFirstLetterFilterNetwork()
    {
        $count = $this->repo->countTleosByFirstLetter('m', NetworkMediumEnum::RADIO, false);
        $this->assertEquals(0, $count);

        $count = $this->repo->countTleosByFirstLetter('m', NetworkMediumEnum::TV, false);
        $this->assertEquals(1, $count);
    }
}
