<?php

namespace BBC\ProgrammesPagesService\Domain\Entity\Unfetched;

use BBC\ProgrammesPagesService\Domain\Entity\Network;
use BBC\ProgrammesPagesService\Domain\ValueObject\Null\NullNid;

class UnfetchedNetwork extends Network
{
    public function __construct()
    {
        parent::__construct(
            new NullNid(),
            '',
            new UnfetchedImage(),
            new UnfetchedOptions()
        );
    }
}
