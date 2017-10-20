<?php

namespace BBC\ProgrammesPagesService\Domain\Enumeration;

class ContactMediaEnum
{
    const EMAIL = 'email';
    const SMS = 'sms';
    const PHONE = 'phone';
    const FAX = 'fax';
    const ADDRESS = 'address';
    const FACEBOOK = 'facebook';
    const TWITTER = 'twitter';
    const GOOGLE = 'google';
    const SPOTIFY = 'spotify';
    const PINTEREST = 'pinterest';
    const TUMBLR = 'tumblr';
    const STUMBLE_UPON = 'stumble_upon';
    const LINKEDIN = 'linkedin';
    const REDDIT = 'reddit';
    const DIGG = 'digg';
    const INSTAGRAM = 'instagram';
    const OTHER = 'other';

    public static function validValues(): array
    {
        return [
            self::EMAIL,
            self::SMS,
            self::PHONE,
            self::FAX,
            self::ADDRESS,
            self::FACEBOOK,
            self::TWITTER,
            self::GOOGLE,
            self::SPOTIFY,
            self::PINTEREST,
            self::TUMBLR,
            self::STUMBLE_UPON,
            self::LINKEDIN,
            self::REDDIT,
            self::DIGG,
            self::INSTAGRAM,
            self::OTHER,
        ];
    }
}
