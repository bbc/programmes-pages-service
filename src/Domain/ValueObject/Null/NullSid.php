<?php

namespace BBC\ProgrammesPagesService\Domain\ValueObject\Null;

use BBC\ProgrammesPagesService\Domain\ValueObject\Sid;

class NullSid extends Sid
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
