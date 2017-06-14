<?php

namespace BBC\ProgrammesPagesService\Domain\Entity\Unfetched;

use BBC\ProgrammesPagesService\Domain\Entity\ProgrammeItem;
use BBC\ProgrammesPagesService\Domain\Enumeration\MediaTypeEnum;
use BBC\ProgrammesPagesService\Domain\ValueObject\Null\NullPid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;

class UnfetchedProgrammeItem extends ProgrammeItem
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
            MediaTypeEnum::UNKNOWN,
            0,
            new UnfetchedOptions()
        );
    }
}
