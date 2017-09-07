<?php

namespace Tests\BBC\ProgrammesPagesService\Service\SegmentsService;

use BBC\ProgrammesPagesService\Domain\Entity\Segment;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Service\SegmentsService;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractSegmentsServiceTest extends AbstractServiceTest
{
    public function setUp()
    {
        $this->setUpCache();
        $this->setUpRepo('SegmentRepository');
        $this->setUpMapper('SegmentMapper', function ($dbSegment) {
            return $this->createConfiguredMock(Segment::class, ['getPid' => new Pid($dbSegment['pid'])]);
        });
    }

    protected function service()
    {
        return new SegmentsService($this->mockRepository, $this->mockMapper, $this->mockCache);
    }
}
