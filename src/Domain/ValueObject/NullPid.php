<?php

namespace BBC\ProgrammesPagesService\Domain\ValueObject;

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
