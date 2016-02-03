<?php

namespace BBC\ProgrammesPagesService\Domain\ValueObject;

use DateInterval;
use DateTimeImmutable;
use JsonSerializable;

class Duration implements JsonSerializable
{
    /** @var DateInterval */
    private $interval;

    public function __construct(string $duration)
    {
        $this->interval = new DateInterval($duration);
        return $this;
    }

    public function getSeconds(): int
    {
        $reference = new DateTimeImmutable();
        $endTime = $reference->add($this->interval);
        return $endTime->getTimestamp() - $reference->getTimestamp();
    }

    public function __toString(): string
    {
        return $this->interval->format('%d days %h hours %m minutes %s seconds');
    }

    public function jsonSerialize(): int
    {
        return $this->getSeconds();
    }

    public function formatMysql(): string
    {
        return $this->getSeconds();
    }
}
