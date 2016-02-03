<?php

namespace BBC\ProgrammesPagesService\Domain\ValueObject;

use JsonSerializable;

class Pid implements JsonSerializable
{
    private $pid;

    public function __construct(string $pid)
    {
        // @todo - validate and throw exceptions
        $this->pid = $pid;
    }

    public function getValue(): string
    {
        return $this->pid;
    }

    public function __toString(): string
    {
        return $this->getValue();
    }

    public function jsonSerialize()
    {
        return $this->getValue();
    }
}
