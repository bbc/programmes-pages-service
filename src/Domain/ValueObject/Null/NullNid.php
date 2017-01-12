<?php

namespace BBC\ProgrammesPagesService\Domain\ValueObject\Null;

use BBC\ProgrammesPagesService\Domain\ValueObject\Nid;

class NullNid extends Nid
{
    public function __construct()
    {
    }

    public function __toString(): string
    {
         return '';
    }

    public function jsonSerialize()
    {
        return null;
    }
}
