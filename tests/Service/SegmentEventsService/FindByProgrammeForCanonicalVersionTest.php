<?php

namespace Tests\BBC\ProgrammesPagesService\Service\SegmentEventsService;

use BBC\ProgrammesPagesService\Domain\Entity\ProgrammeItem;
use BBC\ProgrammesPagesService\Domain\Entity\SegmentEvent;

class FindByProgrammeForCanonicalVersionTest extends AbstractSegmentEventsServiceTest
{
    /**
     * @dataProvider paginationProvider
     */
    public function testCommunicationWithRepository(int $expectedLimit, int $expectedOffset, array $paramsPagination)
    {
        $programmeItem = $this->createConfiguredMock(ProgrammeItem::class, ['getDbId' => 1]);

        $this->mockRepository->expects($this->once())
            ->method('findByProgrammeForCanonicalVersion')
            ->with($programmeItem->getDbId(), $expectedLimit, $expectedOffset);

        $this->service()->findByProgrammeForCanonicalVersion($programmeItem, ...$paramsPagination);
    }

    public function paginationProvider(): array
    {
        return [
            // expected limit, expected offset, user pagination params
            'CASE: default pagination' => [300, 0, []],
            'CASE: custom pagination' => [3, 12, [3, 5]],
        ];
    }

    public function testArrayOfSegmentEventIsReceivedWhenResultsFound()
    {
        $this->mockRepository->method('findByProgrammeForCanonicalVersion')->willReturn([['pid' => 'sg000001'], ['pid' => 'sg000002']]);

        $segmentEvents = $this->service()->findByProgrammeForCanonicalVersion($this->createMock(ProgrammeItem::class));

        $this->assertCount(2, $segmentEvents);
        $this->assertContainsOnly(SegmentEvent::class, $segmentEvents);
        $this->assertEquals('sg000001', $segmentEvents[0]->getPid());
        $this->assertEquals('sg000002', $segmentEvents[1]->getPid());
    }

    public function testEmptyArrayIsReceivedWhenNoResultsFound()
    {
        $this->mockRepository->method('findByProgrammeForCanonicalVersion')->willReturn([]);

        $result = $this->service()->findByProgrammeForCanonicalVersion($this->createMock(ProgrammeItem::class));

        $this->assertEquals([], $result);
    }
}
