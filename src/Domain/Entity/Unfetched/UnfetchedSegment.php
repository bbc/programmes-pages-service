<?php

namespace BBC\ProgrammesPagesService\Domain\Entity\Unfetched;

use BBC\ProgrammesPagesService\Domain\Entity\Segment;
use BBC\ProgrammesPagesService\Domain\ValueObject\Null\NullPid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;

class UnfetchedSegment extends Segment
{
    public function __construct()
    {
        parent::__construct(
            0,
            new NullPid(),
            '',
            new Synopses('', '', ''),
            0,
            ''
        );
    }
}
