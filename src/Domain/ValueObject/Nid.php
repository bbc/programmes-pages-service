<?php

namespace BBC\ProgrammesPagesService\Domain\ValueObject;

use InvalidArgumentException;
use JsonSerializable;

class Nid implements JsonSerializable
{
    private $nid;

    public function __construct(string $nid)
    {
        if (!preg_match('/^[0-9a-z_]{1,}$/', $nid)) {
            throw new InvalidArgumentException('Could not create a Nid from string "' . $nid . '". Expected an lowercase alphanumeric string that allows underscores');
        }

        $this->nid = $nid;
    }

    public function __toString(): string
    {
         return $this->nid;
    }

    public function jsonSerialize()
    {
        return (string) $this;
    }
}
