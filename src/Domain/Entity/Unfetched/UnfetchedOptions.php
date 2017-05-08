<?php

namespace BBC\ProgrammesPagesService\Domain\Entity\Unfetched;

use BBC\ProgrammesPagesService\Domain\Entity\Options;

class UnfetchedOptions extends Options
{
    public function __construct()
    {
        parent::__construct([]);
    }
}
