<?php

namespace BBC\ProgrammesPagesService\Domain\Entity\Unfetched;

use BBC\ProgrammesPagesService\Domain\Entity\Group;
use BBC\ProgrammesPagesService\Domain\ValueObject\Null\NullPid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;

class UnfetchedGroup extends Group
{
    public function __construct()
    {
        parent::__construct(
            [],
            new NullPid(),
            '',
            '',
            new Synopses(''),
            new UnfetchedImage(),
            0,
            0,
            0,
            new UnfetchedOptions()
        );
    }
}
