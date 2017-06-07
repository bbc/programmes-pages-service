<?php

namespace BBC\ProgrammesPagesService\Domain\Enumeration;

class AvailabilityStatusEnum
{
    public const AVAILABLE = 'available';
    public const FUTURE = 'future';
    public const PENDING = 'pending';

    public static function validValues(): array
    {
        return [self::AVAILABLE, self::FUTURE, self::PENDING];
    }
}
