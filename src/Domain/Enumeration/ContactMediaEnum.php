<?php

namespace BBC\ProgrammesPagesService\Domain\Enumeration;

class ContactMediaEnum
{
    public const EMAIL = 'email';
    public const SMS = 'sms';
    public const PHONE = 'phone';
    public const FAX = 'fax';
    public const ADDRESS = 'address';
    public const FACEBOOK = 'facebook';
    public const TWITTER = 'twitter';
    public const GOOGLE = 'google';
    public const SPOTIFY = 'spotify';
    public const PINTEREST = 'pinterest';
    public const TUMBLR = 'tumblr';
    public const STUMBLE_UPON = 'stumble_upon';
    public const LINKEDIN = 'linkedin';
    public const REDDIT = 'reddit';
    public const DIGG = 'digg';
    public const INSTAGRAM = 'instagram';
    public const OTHER = 'other';

    public const VALID_MEDIA = [
        self::EMAIL => true,
        self::SMS => true,
        self::PHONE => true,
        self::FAX => true,
        self::ADDRESS => true,
        self::FACEBOOK => true,
        self::TWITTER => true,
        self::GOOGLE => true,
        self::SPOTIFY => true,
        self::PINTEREST => true,
        self::TUMBLR => true,
        self::STUMBLE_UPON => true,
        self::LINKEDIN => true,
        self::REDDIT => true,
        self::DIGG => true,
        self::INSTAGRAM => true,
        self::OTHER => true,
    ];
}
