<?php

namespace Tests\BBC\ProgrammesPagesService\Service\SegmentEventsService;

use BBC\ProgrammesPagesService\Domain\Entity\SegmentEvent;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\SegmentEventMapper;
use BBC\ProgrammesPagesService\Service\SegmentEventsService;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractSegmentEventsServiceTest extends AbstractServiceTest
{
    public function setUp()
    {
        $this->setUpCache();
        $this->setUpRepo('SegmentEventRepository');
        $this->setUpMapper(SegmentEventMapper::class, function ($dbSegment) {
            return $this->createConfiguredMock(SegmentEvent::class, ['getpid' => new Pid($dbSegment['pid'])]);
        });
    }

    protected function service()
    {
        return new SegmentEventsService($this->mockRepository, $this->mockMapper, $this->mockCache);
    }
}
