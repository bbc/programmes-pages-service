<?php

namespace Tests\BBC\ProgrammesPagesService\Service\SegmentEventsService;

use BBC\ProgrammesPagesService\Domain\Entity\SegmentEvent;
use BBC\ProgrammesPagesService\Domain\Entity\Version;

class FindByVersionWithContributionsTest extends AbstractSegmentEventsServiceTest
{
    /**
     * @dataProvider paginationProvider
     */
    public function testCommunicationWithRepository(int $expectedLimit, int $expectedOffset, array $paramsPagination)
    {
        $version = $this->createConfiguredMock(Version::class, ['getDbId' => 1]);

        $this->mockRepository->expects($this->once())
            ->method('findByVersionWithContributions')
            ->with([$version->getDbId()], $expectedLimit, $expectedOffset);

        $this->service()->findByVersionWithContributions($version, ...$paramsPagination);
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
        $this->mockRepository->method('findByVersionWithContributions')->willReturn([['pid' => 'sg000001'], ['pid' => 'sg000002']]);

        $segmentEvents = $this->service()->findByVersionWithContributions($this->createMock(Version::class));

        $this->assertCount(2, $segmentEvents);
        $this->assertContainsOnly(SegmentEvent::class, $segmentEvents);
        $this->assertEquals('sg000001', $segmentEvents[0]->getPid());
        $this->assertEquals('sg000002', $segmentEvents[1]->getPid());
    }

    public function testEmptyArrayIsReceivedWhenNoResultsFound()
    {
        $this->mockRepository->method('findByVersionWithContributions')->willReturn([]);

        $result = $this->service()->findByVersionWithContributions($this->createMock(Version::class));

        $this->assertEquals([], $result);
    }
}
