<?php

namespace BBC\ProgrammesPagesService\Domain\ValueObject;

use InvalidArgumentException;
use JsonSerializable;

class Pid implements JsonSerializable
{
    private $pid;

    public function __construct(string $pid)
    {
        if (!preg_match('/^[0-9|b-d|f-h|j-n|p-t|v-z]{8,}$/i', $pid)) {
            throw new InvalidArgumentException('Could not create a Pid from string "' . $pid . '". Expected an alphanumeric string of at least 8 characters that does not contain any vowels');
        }

        $this->pid = $pid;
    }

    public function __toString(): string
    {
         return $this->pid;
    }

    public function jsonSerialize()
    {
        return (string) $this;
    }
}
