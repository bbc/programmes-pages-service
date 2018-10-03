<?php

namespace BBC\ProgrammesPagesService\Domain\Entity\Unfetched;

use BBC\ProgrammesPagesService\Domain\Entity\MasterBrand;
use BBC\ProgrammesPagesService\Domain\ValueObject\Null\NullMid;

class UnfetchedMasterBrand extends MasterBrand
{
    public function __construct()
    {
        parent::__construct(
            new NullMid(),
            '',
            new UnfetchedImage(),
            new UnfetchedNetwork(),
            false
        );
    }
}
