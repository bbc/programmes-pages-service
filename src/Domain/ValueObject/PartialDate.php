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
     * Accepts an ISO8601-style date (YYYY-MM-DD) where the month and day
     * components are optional. e.g. "2015", "2015-01", "2015-01-14"
     *
     * @param string $dateString
     */
    public function __construct(string $dateString)
    {
        $matches = [];

        $result = preg_match('/^([\d]{4})(?:-([0-1][0-9]))?(?:-([0-3][0-9]))?$/', $dateString, $matches);

        if (!$result) {
            $this->throwInvalidConstructionException($dateString);
        }

        $matches[2] = (int) ($matches[2] ?? 0);
        $matches[3] = (int) ($matches[3] ?? 0);

        $check = checkdate(
            $matches[2] == 0 ? 1 : $matches[2],
            $matches[3] == 0 ? 1 : $matches[3],
            $matches[1]
        );

        if (!$check) {
            $this->throwInvalidConstructionException($dateString);
        }

        $this->year = $matches[1];
        $this->month = $matches[2];
        $this->day = $matches[3];
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

    private function throwInvalidConstructionException($dateString)
    {
        throw new InvalidArgumentException('Could not create a PartialDate from string "' . $dateString . '". Expected a date in the format "YYYY-MM-DD", "YYYY-MM" or "YYYY"');
    }
}
