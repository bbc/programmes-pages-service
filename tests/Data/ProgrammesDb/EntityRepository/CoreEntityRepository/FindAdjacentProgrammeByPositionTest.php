<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;
use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;
use InvalidArgumentException;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository::<public>
 */
class FindAdjacentProgrammeByPositionTest extends AbstractDatabaseTest
{
    public function testFindAdjacentProgrammeByPosition()
    {
        $this->loadFixtures(['SiblingsFixture']);

        /** @var CoreEntityRepository $repo */
        $repo = $this->getRepository('ProgrammesPagesService:CoreEntity');

        // Use a loop rather than a dataProvider so we avoid recreating the
        // fixture data on every iteration
        foreach ($this->siblingsPositionData() as $data) {
            list($entityType, $parentDbId, $position, $direction, $expectedPid) = $data;

            $entity = $repo->findAdjacentProgrammeByPosition(
                $parentDbId,
                $position,
                $entityType,
                $direction
            );

            $this->assertEquals($expectedPid, $entity['pid']);
        }
    }

    public function siblingsPositionData()
    {
        $episodeParentDbId = $this->getCoreEntityDbId('b00swyx1');
        $seriesParentDbId = $this->getCoreEntityDbId('b010t19z');

        return [

            //Episodes
            //Ordering by position next
            ['Episode', $episodeParentDbId, 0, 'next', 'b00swgkn'],
            ['Episode', $episodeParentDbId, 1, 'next', 'b00syxx6'],
            ['Episode', $episodeParentDbId, 2, 'next', 'b00syxx7'],

            //Ensuring items at the end don't have a next
            ['Episode', $episodeParentDbId, 3, 'next', null],

            //Ensuring items at the start don't have a previous
            ['Episode', $episodeParentDbId, 1, 'previous', null],

            //Ordering by position previous
            ['Episode', $episodeParentDbId, 2, 'previous', 'b00swgkn'],
            ['Episode', $episodeParentDbId, 3, 'previous', 'b00syxx6'],
            ['Episode', $episodeParentDbId, 4, 'previous', 'b00syxx7'],

            //Clips
            //Ordering by position next
            ['Clip', $episodeParentDbId, 0, 'next', 'p00hv9yz'],
            ['Clip', $episodeParentDbId, 1, 'next', 'p008k0l5'],

            //Ensuring items at the end don't have a next
            ['Clip', $episodeParentDbId, 2, 'next', null],

            //Ensuring items at the start don't have a previous
            ['Clip', $episodeParentDbId, 1, 'previous', null],


            //Ordering by position previous
            ['Clip', $episodeParentDbId, 2, 'previous', 'p00hv9yz'],
            ['Clip', $episodeParentDbId, 3, 'previous', 'p008k0l5'],

            //Series
            //Ordering by position next
            ['Series', $seriesParentDbId, 0, 'next', 'b00swyx1'],
            ['Series', $seriesParentDbId, 1, 'next', 'b00swyx2'],
            ['Series', $seriesParentDbId, 2, 'next', 'b00swyx3'],

            //Ensuring items at the end don't have a next
            ['Series', $seriesParentDbId, 3, 'next', null],

            //Ensuring items at the start don't have a previous
            ['Series', $seriesParentDbId, 1, 'previous', null],

            //Ordering by position previous
            ['Series', $seriesParentDbId, 2, 'previous', 'b00swyx1'],
            ['Series', $seriesParentDbId, 3, 'previous', 'b00swyx2'],
            ['Series', $seriesParentDbId, 4, 'previous', 'b00swyx3'],

        ];
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidTypeForFindAdjacentProgrammeByPosition()
    {
        $repo = $this->getRepository('ProgrammesPagesService:CoreEntity');
        $repo->findAdjacentProgrammeByPosition(
            999,
            1,
            'INVALID_TYPE',
            'previous'
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidDirectionForFindAdjacentProgrammeByPosition()
    {
        $repo = $this->getRepository('ProgrammesPagesService:CoreEntity');
        $repo->findAdjacentProgrammeByPosition(
            999,
            1,
            'Episode',
            'UP'
        );
    }
}
