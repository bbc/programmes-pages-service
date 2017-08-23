<?php

namespace BBC\ProgrammesPagesService\Domain;

use Cake\Chronos\Chronos;
use DateTimeImmutable;
use DateTimeZone;

/**
 * A Singleton class to help us ensure that our concept of "now" remains
 * consistent across multiple calls. This also allows for easy spoofing of when
 * "now" is.
 */
class ApplicationTime
{
    /** @var DateTimeImmutable|null  */
    private static $appTime = null;

    /** @var DateTimeImmutable|null */
    private static $localTime = null;

    /** @var DateTimeZone|null */
    private static $localTimeZone = null;

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
     * When getting a time, we often want it in local time.
     * This is a separate method to getTime() as some code (such as Doctrine) ignores timezones and just uses the date
     * and time. So.... be careful!
     */
    public static function getLocalTime(): DateTimeImmutable
    {
        if (null === static::$localTime) {
            $time = self::getTime();
            static::$localTime = $time->setTimezone(self::getLocalTimeZone());
        }

        return static::$localTime;
    }

    public static function getLocalTimeZone(): DateTimeZone
    {
        if (null === self::$localTimeZone) {
            self::setLocalTimeZone();
        }
        return self::$localTimeZone;
    }

    public static function getTruncatedTime(): DateTimeImmutable
    {
        $time = static::getTime();
        return $time->setTime($time->format('H'), $time->format('i'), 0);
    }

    public static function getCurrent3MinuteWindow(): DateTimeImmutable
    {
        $time = static::getTime();
        $currentWindow = floor($time->format('i') / 3) * 3;
        return $time->setTime($time->format('H'), $currentWindow, 0);
    }

    /**
     * Since it is a timestamp that is passed it, this DateTimeImmutable will always be UTC
     *
     * @param int|null $appTime A timestamp
     */
    public static function setTime(int $appTime = null): void
    {
        $timeToSet = $appTime ?? time();
        static::$localTime = null;
        if (class_exists(Chronos::class)) {
            static::$appTime = Chronos::createFromTimestampUTC($timeToSet)
                ->setTimezone(new DateTimeZone('UTC'));
            Chronos::setTestNow(static::$appTime);
        } else {
            static::$appTime = DateTimeImmutable::createFromFormat('U', (string) $timeToSet)
                ->setTimezone(new DateTimeZone('UTC'));
        }
    }

    /**
     * Set the local time zone used by all subsequent requests to getLocalTime()
     * @param string $timezoneString
     */
    public static function setLocalTimeZone(string $timezoneString = 'Europe/London'): void
    {
        self::$localTimeZone = new DateTimeZone($timezoneString);
        self::$localTime = null;
    }

    /**
     * Blanks out any pre-set value of the time, so that a new value is
     * returned the next time we call this. Useful when testing.
     */
    public static function blank(): void
    {
        static::$appTime = null;
        static::$localTimeZone = null;
        static::$localTime = null;
    }

    protected function __construct()
    {
    }
}
