<?php

namespace BBC\ProgrammesPagesService\Domain\ValueObject;

use InvalidArgumentException;
use JsonSerializable;

class Sid implements JsonSerializable
{
    private $sid;

    public function __construct(string $sid)
    {
        if (!preg_match('/^[0-9a-z_]{1,}$/', $sid)) {
            throw new InvalidArgumentException('Could not create a Sid from string "' . $sid . '". Expected an lowercase alphanumeric string that allows underscores');
        }

        $this->sid = $sid;
    }

    public function __toString(): string
    {
         return $this->sid;
    }

    public function jsonSerialize()
    {
        return (string) $this;
    }
}
