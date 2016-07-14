<?php

namespace BBC\ProgrammesPagesService\Domain\Entity\Unfetched;

use BBC\ProgrammesPagesService\Domain\Entity\Version;
use BBC\ProgrammesPagesService\Domain\ValueObject\NullPid;

class UnfetchedVersion extends Version
{
    public function __construct()
    {
        parent::__construct(
            0,
            new NullPid(),
            new UnfetchedProgrammeItem()
        );
    }
}
