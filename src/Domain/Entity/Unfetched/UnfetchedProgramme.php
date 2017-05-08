<?php

namespace BBC\ProgrammesPagesService\Domain\Entity\Unfetched;

use BBC\ProgrammesPagesService\Domain\Entity\Options;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Domain\ValueObject\Null\NullPid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;

class UnfetchedProgramme extends Programme
{
    public function __construct()
    {
        parent::__construct(
            [0],
            new NullPid(),
            '',
            '',
            new Synopses('', '', ''),
            new UnfetchedImage(),
            0,
            0,
            false,
            false,
            false,
            0,
            new Options()
        );
    }
}
