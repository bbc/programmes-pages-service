<?php

namespace BBC\ProgrammesPagesService\Domain;

use DateTimeImmutable;
use DateTimeZone;

/**
 * A Singleton class to help us ensure that our concept of "now" remains
 * consistent across multiple calls. This also allows for easy spoofing of when
 * "now" is.
 */
class ApplicationTime
{
    /** @var DateTimeImmutable  */
    private static $appTime = null;

    /**
     * Fetch the current DateTime
     *
     * @return DateTimeImmutable
     */
    public static function getTime(): DateTimeImmutable
    {
        if (null === static::$appTime) {
            static::setTime();
        }

        return static::$appTime;
    }

    /**
     * When getting a time, we often want it in local UK time.
     * This is a separate method to getTime() as some code (such as Doctrine) ignores timezones and just uses the date
     * and time. So.... be careful!
     *
     * @param string $timezoneString
     * @return DateTimeImmutable
     */
    public static function getLocalTime(string $timezoneString = 'Europe/London'): DateTimeImmutable
    {
        if (null === static::$appTime) {
            static::setTime();
        }

        return static::$appTime->setTimezone(new DateTimeZone($timezoneString));
    }

    public static function getTruncatedTime()
    {
        static::getTime();
        return static::$appTime->setTime(static::$appTime->format('H'), static::$appTime->format('i'), 0);
    }

    public static function getCurrent3MinuteWindow()
    {
        static::getTime();

        $currentWindow = floor(static::$appTime->format('i') / 3) * 3;
        return static::$appTime->setTime(static::$appTime->format('H'), $currentWindow, 0);
    }

    /**
     * Since it is a timestamp that is passed it, this DateTimeImmutable will always be UTC
     *
     * @param int|null $appTime A timestamp
     */
    public static function setTime(int $appTime = null)
    {
        static::$appTime = DateTimeImmutable::createFromFormat('U', $appTime ?? time())
            ->setTimezone(new DateTimeZone('UTC'));
    }

    /**
     * Blanks out any pre-set value of the time, so that a new value is
     * returned the next time we call this. Useful when testing.
     */
    public static function blank()
    {
        static::$appTime = null;
    }

    protected function __construct()
    {
    }
}
