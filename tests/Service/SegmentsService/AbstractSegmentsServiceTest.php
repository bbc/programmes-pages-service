<?php

namespace Tests\BBC\ProgrammesPagesService\Service\SegmentsService;

use BBC\ProgrammesPagesService\Service\SegmentsService;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractSegmentsServiceTest extends AbstractServiceTest
{
    public function setUp()
    {
        $this->setUpRepo('SegmentRepository');
        $this->setUpMapper('SegmentMapper', 'segmentFromDbData');
    }

    protected function versionsFromDbData(array $entities)
    {
        return array_map([$this, 'segmentFromDbData'], $entities);
    }

    protected function segmentFromDbData(array $entity)
    {
        $mockVersion = $this->createMock(self::ENTITY_NS . 'Segment');
        $mockVersion->method('getPid')->willReturn($entity['pid']);
        return $mockVersion;
    }

    protected function service()
    {
        return new SegmentsService($this->mockRepository, $this->mockMapper);
    }
}
