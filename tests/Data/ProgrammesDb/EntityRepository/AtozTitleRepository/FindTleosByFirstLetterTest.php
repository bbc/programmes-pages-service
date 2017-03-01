<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\AtozTitleRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\AtozTitleRepository;
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
        $letters = $this->repo->findAllLetters();
        $expectedLetters = ['@', 'm', 't', 'w'];
        $this->assertEquals($expectedLetters, $letters);
    }

    public function testFindByFirstLetterAt()
    {
        $tleos = $this->repo->findTleosByFirstLetter('@', false, null, 0);
        $this->assertCount(1, $tleos);
        $this->assertEquals('b0000002', $tleos[0]['coreEntity']['pid']);
    }

    public function testFindByFirstLetterW()
    {
        $tleos = $this->repo->findTleosByFirstLetter('w', false, null, 0);
        $this->assertCount(1, $tleos);
        $this->assertEquals('b0000001', $tleos[0]['coreEntity']['pid']);
    }

    public function testGetEmbargoed()
    {
        $tleos = $this->repo->findTleosByFirstLetter('p', false, null, 0);
        $this->assertEmpty($tleos);
    }

    public function testFilterAvailable()
    {
        $tleos = $this->repo->findTleosByFirstLetter('w', true, null, 0);
        $this->assertEmpty($tleos);

        $tleos = $this->repo->findTleosByFirstLetter('@', true, null, 0);
        $this->assertCount(1, $tleos);
        $this->assertEquals('b0000002', $tleos[0]['coreEntity']['pid']);
    }

    public function testCountByFirstLetter()
    {
        $count = $this->repo->countTleosByFirstLetter('@', false);
        $this->assertEquals(1, $count);
    }

    public function testCountByFirstLetterFilterAvailable()
    {
        $count = $this->repo->countTleosByFirstLetter('w', true);
        $this->assertEquals(0, $count);

        $count = $this->repo->countTleosByFirstLetter('@', true);
        $this->assertEquals(1, $count);
    }
}
