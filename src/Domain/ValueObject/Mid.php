<?php

namespace BBC\ProgrammesPagesService\Domain\ValueObject;

use InvalidArgumentException;
use JsonSerializable;

class Mid implements JsonSerializable
{
    private $mid;

    public function __construct(string $mid)
    {
        if (!preg_match('/^[0-9a-z_]{1,}$/', $mid)) {
            throw new InvalidArgumentException('Could not create a Mid from string "' . $mid . '". Expected an lowercase alphanumeric string that allows underscores');
        }

        $this->mid = $mid;
    }

    public function __toString(): string
    {
         return $this->mid;
    }

    public function jsonSerialize()
    {
        return (string) $this;
    }
}
