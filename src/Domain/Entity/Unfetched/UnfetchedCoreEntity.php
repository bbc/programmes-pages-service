<?php

namespace BBC\ProgrammesPagesService\Domain\Entity\Unfetched;

use BBC\ProgrammesPagesService\Domain\Entity\CoreEntity;
use BBC\ProgrammesPagesService\Domain\ValueObject\Null\NullPid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;

class UnfetchedCoreEntity extends CoreEntity
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
            0,
            new UnfetchedOptions()
        );
    }
}
