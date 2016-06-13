<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;

use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository::<public>
 */
class CoreEntityRepositoryFindAdjacentProgrammeByReleaseDateTest extends AbstractDatabaseTest
{
    public function testFindAdjacentProgrammeByReleaseDate()
    {
        $this->loadFixtures(['SiblingsFixture']);
        $repo = $this->getRepository('ProgrammesPagesService:CoreEntity');

        // Use a loop rather than a dataProvider so we avoid recreating the
        // fixture data on every iteration
        foreach ($this->siblingsReleaseDateData() as $data) {
            list($entityType, $parentDbId, $releaseYear, $direction, $expectedPid) = $data;

            $entity = $repo->findAdjacentProgrammeByReleaseDate(
                $parentDbId,
                new PartialDate($releaseYear),
                $entityType,
                $direction
            );

            $this->assertEquals($expectedPid, $entity['pid']);
        }
    }

    public function siblingsReleaseDateData()
    {
        $episodeParentDbId = $this->getCoreEntityDbId('b00swyx1');

        return [
            //Episodes
            //Ordering by position next
            ['Episode', $episodeParentDbId, 2013, 'next', 'b00swgkn'],
            ['Episode', $episodeParentDbId, 2014, 'next', 'b00syxx6'],
            ['Episode', $episodeParentDbId, 2015, 'next', 'b00syxx7'],

            //Ensuring items at the end don't have a next
            ['Episode', $episodeParentDbId, 2016, 'next', null],

            //Ensuring items at the start don't have a previous
            ['Episode', $episodeParentDbId, 2014, 'previous', null],

            //Ordering by position previous
            ['Episode', $episodeParentDbId, 2015, 'previous', 'b00swgkn'],
            ['Episode', $episodeParentDbId, 2016, 'previous', 'b00syxx6'],
            ['Episode', $episodeParentDbId, 2017, 'previous', 'b00syxx7'],

            //Clips
            //Ordering by position next
            ['Clip', $episodeParentDbId, 2013, 'next', 'p00hv9yz'],
            ['Clip', $episodeParentDbId, 2014, 'next', 'p008k0l5'],

            //Ensuring items at the end don't have a next
            ['Clip', $episodeParentDbId, 2015, 'next', null],

            //Ensuring items at the start don't have a previous
            ['Clip', $episodeParentDbId, 2014, 'previous', null],

            //Ordering by position previous
            ['Clip', $episodeParentDbId, 2015, 'previous', 'p00hv9yz'],
            ['Clip', $episodeParentDbId, 2016, 'previous', 'p008k0l5'],
        ];
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidTypeForFindAdjacentProgrammeByReleaseDate()
    {
        $repo = $this->getRepository('ProgrammesPagesService:CoreEntity');
        $repo->findAdjacentProgrammeByReleaseDate(
            999,
            new PartialDate(2016),
            'Series',
            'previous'
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidDirectionForFindAdjacentProgrammeByReleaseDate()
    {
        $repo = $this->getRepository('ProgrammesPagesService:CoreEntity');
        $repo->findAdjacentProgrammeByReleaseDate(
            999,
            new PartialDate(2016),
            'Episode',
            'UP'
        );
    }
}
