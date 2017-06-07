<?php

namespace BBC\ProgrammesPagesService\Domain\Enumeration;

class MediaTypeEnum
{
    public const AUDIO = 'audio';
    public const VIDEO = 'audio_video';
    public const UNKNOWN = '';

    public static function validValues(): array
    {
        return [self::AUDIO, self::VIDEO, self::UNKNOWN];
    }
}
