<?php

namespace BBC\ProgrammesPagesService\Domain\ValueObject;

use DateTime;
use InvalidArgumentException;
use JsonSerializable;

/**
 * A Date that allows the Day or Month to be null so you can specify a
 * non-specific date such as "2015", or "January 2015", in addition to a
 * specific date "14th January 2015"
 */
class PartialDate implements JsonSerializable
{
    private $year;
    private $month;
    private $day;

    /**
     * Accepts a date where the month and day
     * components are optional. e.g. "2015", "2015-01", "2015-01-14"
     */
    public function __construct(int $year, int $month = 0, int $day = 0)
    {
        list($checkMonth, $checkDay) = $this->normaliseMonthAndDay($month, $day);
        $check = checkdate(
            $checkMonth,
            $checkDay,
            $year
        );

        if (!$check) {
            $this->throwInvalidConstructionException($year, $month, $day);
        }

        $this->year = $year;
        $this->month = $month;
        $this->day = $day;
    }

    public function __toString(): string
    {
        return sprintf('%d-%02d-%02d', $this->year, $this->month, $this->day);
    }

    public function jsonSerialize()
    {
        return (string) $this;
    }

    public function formatMysql(): string
    {
        return sprintf('%d-%02d-%02d', $this->year, $this->month, $this->day);
    }

    public function asDateTime(): DateTime
    {
        $year = $this->year;
        list($month, $day) = $this->normaliseMonthAndDay($this->month, $this->day);
        return DateTime::createFromFormat('Y-m-d H:i:s e', "$year-$month-$day 00:00:00 UTC");
    }

    private function normaliseMonthAndDay(int $month, int $day): array
    {
        return [
            (($month == 0) ? 1 : $month),
            (($day == 0) ? 1 : $day),
        ];
    }

    private function throwInvalidConstructionException($year, $month, $day)
    {
        throw new InvalidArgumentException("Could not create a PartialDate from parameters year=$year, month=$month, day=$day. Expected a valid partial date");
    }
}
