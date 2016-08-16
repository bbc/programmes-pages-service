<?php

namespace Tests\BBC\ProgrammesPagesService\Service\SegmentEventsService;

use BBC\ProgrammesPagesService\Service\SegmentEventsService;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractSegmentEventsServiceTest extends AbstractServiceTest
{
    public function setUp()
    {
        $this->setUpRepo('SegmentEventRepository');
        $this->setUpMapper('SegmentEventMapper', 'segmentEventFromDbData');
    }

    protected function segmentEventsFromDbData(array $entities)
    {
        return array_map([$this, 'segmentEventFromDbData'], $entities);
    }

    protected function segmentEventFromDbData(array $entity)
    {
        $mockSegmentEvent = $this->createMock(self::ENTITY_NS . 'SegmentEvent');
        $mockSegmentEvent->method('getPid')->willReturn($entity['pid']);
        return $mockSegmentEvent;
    }

    protected function service()
    {
        return new SegmentEventsService($this->mockRepository, $this->mockMapper);
    }
}
