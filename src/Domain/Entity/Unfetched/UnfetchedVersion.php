<?php

namespace BBC\ProgrammesPagesService\Domain\Entity\Unfetched;

use BBC\ProgrammesPagesService\Domain\Entity\Version;
use BBC\ProgrammesPagesService\Domain\ValueObject\Null\NullPid;

class UnfetchedVersion extends Version
{
    public function __construct()
    {
        parent::__construct(
            0,
            new NullPid(),
            new UnfetchedProgrammeItem(),
            false,
            false,
            0
        );
    }
}
