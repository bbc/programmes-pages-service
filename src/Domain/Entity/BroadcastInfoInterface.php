<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use DateTimeImmutable;

interface BroadcastInfoInterface
{
    public function getEndAt(): DateTimeImmutable;
    public function getService(): Service;
    public function getStartAt(): DateTimeImmutable;
    public function isOnAir(): bool;
    public function isOnAirAt(DateTimeImmutable $dateTime): bool;
}
