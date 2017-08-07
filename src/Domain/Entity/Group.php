<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

abstract class Group extends CoreEntity
{
    public function isTlec(): bool
    {
        return true;
    }

    public function isTleo(): bool
    {
        return true;
    }
}
