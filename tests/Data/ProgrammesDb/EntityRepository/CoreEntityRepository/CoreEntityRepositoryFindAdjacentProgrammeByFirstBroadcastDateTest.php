<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository::<public>
 */
class CoreEntityRepositoryFindAdjacentProgrammeByFirstBroadcastDateTest extends AbstractDatabaseTest
{
    public function testFindAdjacentProgrammeByFirstBroadcastDate()
    {
        $this->loadFixtures(['SiblingsFixture']);
        /** @var CoreEntityRepository $repo */
        $repo = $this->getRepository('ProgrammesPagesService:CoreEntity');

        // Use a loop rather than a dataProvider so we avoid recreating the
        // fixture data on every iteration
        foreach ($this->siblingsFirstBroadcastDateData() as $data) {
            list($entityType, $parentDbId, $firstBroadcastDate, $direction, $expectedPid) = $data;

            $entity = $repo->findAdjacentProgrammeByFirstBroadcastDate(
                $parentDbId,
                $firstBroadcastDate,
                $entityType,
                $direction
            );

            $this->assertEquals($expectedPid, $entity['pid']);
        }
    }

    public function siblingsFirstBroadcastDateData()
    {
        $episodeParentDbId = $this->getCoreEntityDbId('b00swyx1');

        return [
            //Episodes
            //Ordering by position next
            ['Episode', $episodeParentDbId, new \DateTimeImmutable('2013-01-01 00:00:00'), 'next', 'b00swgkn'],
            ['Episode', $episodeParentDbId, new \DateTimeImmutable('2014-01-01 00:00:00'), 'next', 'b00syxx6'],
            ['Episode', $episodeParentDbId, new \DateTimeImmutable('2015-01-01 00:00:00'), 'next', 'b00syxx7'],

            //Ensuring items at the end don't have a next
            ['Episode', $episodeParentDbId, new \DateTimeImmutable('2016-01-01 00:00:00'), 'next', null],

            //Ensuring items at the start don't have a previous
            ['Episode', $episodeParentDbId, new \DateTimeImmutable('2014-01-01 00:00:00'), 'previous', null],

            //Ordering by position previous
            ['Episode', $episodeParentDbId, new \DateTimeImmutable('2015-01-01 00:00:00'), 'previous', 'b00swgkn'],
            ['Episode', $episodeParentDbId, new \DateTimeImmutable('2016-01-01 00:00:00'), 'previous', 'b00syxx6'],
            ['Episode', $episodeParentDbId, new \DateTimeImmutable('2017-01-01 00:00:00'), 'previous', 'b00syxx7'],

            //Clips
            //Ordering by position next
            ['Clip', $episodeParentDbId, new \DateTimeImmutable('2013-01-01 00:00:00'), 'next', null],
            ['Clip', $episodeParentDbId, new \DateTimeImmutable('2014-01-01 00:00:00'), 'next', null],

            //Ensuring items at the end don't have a next
            ['Clip', $episodeParentDbId, new \DateTimeImmutable('2015-01-01 00:00:00'), 'next', null],

            //Ensuring items at the start don't have a previous
            ['Clip', $episodeParentDbId, new \DateTimeImmutable('2014-01-01 00:00:00'), 'previous', null],

            //Ordering by position previous
            ['Clip', $episodeParentDbId, new \DateTimeImmutable('2015-01-01 00:00:00'), 'previous', null],
            ['Clip', $episodeParentDbId, new \DateTimeImmutable('2016-01-01 00:00:00'), 'previous', null],
        ];
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidTypeForFindAdjacentProgrammeByFirstBroadcastDate()
    {
        $repo = $this->getRepository('ProgrammesPagesService:CoreEntity');
        $repo->findAdjacentProgrammeByFirstBroadcastDate(
            999,
            new \DateTimeImmutable('2000-01-01 00:00:00'),
            'Series',
            'previous'
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidDirectionForFindAdjacentProgrammeByFirstBroadcastDate()
    {
        $repo = $this->getRepository('ProgrammesPagesService:CoreEntity');
        $repo->findAdjacentProgrammeByFirstBroadcastDate(
            999,
            new \DateTimeImmutable('2000-01-01 00:00:00'),
            'Episode',
            'UP'
        );
    }
}
