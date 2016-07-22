<?php

namespace BBC\ProgrammesPagesService\Domain\Entity\Unfetched;

use BBC\ProgrammesPagesService\Domain\Entity\Service;
use BBC\ProgrammesPagesService\Domain\ValueObject\Null\NullSid;

class UnfetchedService extends Service
{
    public function __construct()
    {
        parent::__construct(
            0,
            new NullSid(),
            ''
        );
    }
}
