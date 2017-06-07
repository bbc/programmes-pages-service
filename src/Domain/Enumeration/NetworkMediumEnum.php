<?php

namespace BBC\ProgrammesPagesService\Domain\Enumeration;

class NetworkMediumEnum
{
    public const RADIO = 'radio';
    public const TV = 'tv';
    public const UNKNOWN = '';

    public static function validValues(): array
    {
        return [self::RADIO, self::TV, self::UNKNOWN];
    }
}
