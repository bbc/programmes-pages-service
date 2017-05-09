<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity\Unfetched;

use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedSegment;
use PHPUnit\Framework\TestCase;

class UnfetchedSegmentTest extends TestCase
{
    public function testUnfetchedSegment()
    {
        $this->assertInstanceOf(
            'BBC\ProgrammesPagesService\Domain\Entity\Segment',
            new UnfetchedSegment()
        );
    }
}
