<?php

namespace BBC\ProgrammesPagesService\Domain\Entity\Unfetched;

use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\ValueObject\Null\NullPid;

class UnfetchedImage extends Image
{
    public function __construct()
    {
        parent::__construct(
            new NullPid(),
            '',
            '',
            '',
            '',
            ''
        );
    }
}
