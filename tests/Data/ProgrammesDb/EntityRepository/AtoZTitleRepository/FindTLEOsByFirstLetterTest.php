<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\AtoZTitleRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\AtoZTitleRepository;
use BBC\ProgrammesPagesService\Domain\Enumeration\NetworkMediumEnum;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

class FindTLEOsByFirstLetterTest extends AbstractDatabaseTest
{
    /** @var AtoZTitleRepository $repo */
    private $repo;

    public function setUp()
    {
        $this->loadFixtures(['AtoZTitleFixture']);
        $this->enableEmbargoedFilter();
        $this->repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:AtoZTitle');
    }

    public function tearDown()
    {
        $this->disableEmbargoedFilter();
    }

    public function testFindAllLetters()
    {
        $letters = $this->repo->findAllLetters();
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
        $TLEOs = $this->repo->findTLEOsByFirstLetter('@', null, 0);
        $this->assertCount(1, $TLEOs);
        $this->assertEquals('b0000002', $TLEOs[0]['coreEntity']['pid']);
    }

    public function testFindByFirstLetterW()
    {
        $TLEOs = $this->repo->findTLEOsByFirstLetter('w', null, 0);
        $this->assertCount(1, $TLEOs);
        $this->assertEquals('b0000001', $TLEOs[0]['coreEntity']['pid']);
    }

    public function testGetEmbargoed()
    {
        $TLEOs = $this->repo->findTLEOsByFirstLetter('p', null, 0);
        $this->assertEmpty($TLEOs);
    }

    public function testFilterAvailable()
    {
        $TLEOs = $this->repo->findTLEOsByFirstLetter('w', null, 0, null, true);
        $this->assertEmpty($TLEOs);

        $TLEOs = $this->repo->findTLEOsByFirstLetter('@', null, 0, null, true);
        $this->assertCount(1, $TLEOs);
        $this->assertEquals('b0000002', $TLEOs[0]['coreEntity']['pid']);
    }

    public function testFindWithNetworkMedium()
    {
        $TLEOs = $this->repo->findTLEOsByFirstLetter('w', null, 0, NetworkMediumEnum::TV);
        $this->assertEmpty($TLEOs);

        $TLEOs = $this->repo->findTLEOsByFirstLetter('m', null, 0, NetworkMediumEnum::TV);
        $this->assertCount(1, $TLEOs);
        $this->assertEquals('b010t19z', $TLEOs[0]['coreEntity']['pid']);
    }

    public function testCountByFirstLetter()
    {
        $count = $this->repo->countTLEOsByFirstLetter('@');
        $this->assertEquals(1, $count);
    }

    public function testCountByFirstLetterFilterAvailable()
    {
        $count = $this->repo->countTLEOsByFirstLetter('w', null, true);
        $this->assertEquals(0, $count);

        $count = $this->repo->countTLEOsByFirstLetter('@', null, true);
        $this->assertEquals(1, $count);
    }

    public function testCountByFirstLetterFilterNetwork()
    {
        $count = $this->repo->countTLEOsByFirstLetter('m', NetworkMediumEnum::RADIO);
        $this->assertEquals(0, $count);

        $count = $this->repo->countTLEOsByFirstLetter('m', NetworkMediumEnum::TV);
        $this->assertEquals(1, $count);
    }
}
