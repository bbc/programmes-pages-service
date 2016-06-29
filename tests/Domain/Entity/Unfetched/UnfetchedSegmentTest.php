<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity\Unfetched;

use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedSegment;
use PHPUnit_Framework_TestCase;

class UnfetchedSegmentTest extends PHPUnit_Framework_TestCase
{
    public function testUnfetchedSegment()
    {
        $this->assertInstanceOf(
            'BBC\ProgrammesPagesService\Domain\Entity\Segment',
            new UnfetchedSegment()
        );
    }
}
