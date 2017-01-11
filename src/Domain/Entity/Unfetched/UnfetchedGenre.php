<?php

namespace BBC\ProgrammesPagesService\Domain\Entity\Unfetched;

use BBC\ProgrammesPagesService\Domain\Entity\Genre;

class UnfetchedGenre extends Genre
{
    public function __construct()
    {
        parent::__construct(
            [0],
            '',
            '',
            '',
            null
        );
    }
}
