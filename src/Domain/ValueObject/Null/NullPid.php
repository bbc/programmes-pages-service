<?php

namespace BBC\ProgrammesPagesService\Domain\ValueObject\Null;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class NullPid extends Pid
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
