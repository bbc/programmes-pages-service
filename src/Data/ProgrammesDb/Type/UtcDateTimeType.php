<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Type;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeType;
use InvalidArgumentException;

class UtcDateTimeType extends DateTimeType
{
    static private $utc;

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (is_null($value)) {
            return $value;
        }

        if (!$value instanceof DateTimeInterface) {
            throw new InvalidArgumentException('$value must implement DateTimeInterface');
        }

        if ($value instanceof DateTime) {
            // To avoid altering the original DateTime
            $value = clone $value;
        }

        $utcValue = $value->setTimezone(new DateTimeZone('UTC'));

        return parent::convertToDatabaseValue($utcValue, $platform);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?DateTimeInterface
    {
        if (is_null($value) || $value instanceof DateTimeInterface) {
            return $value;
        }

        $converted = DateTimeImmutable::createFromFormat(
            $platform->getDateTimeFormatString(),
            $value,
            self::$utc ? self::$utc : self::$utc = new DateTimeZone('UTC')
        );

        if (!$converted) {
            throw ConversionException::conversionFailedFormat(
                $value,
                $this->getName(),
                $platform->getDateTimeFormatString()
            );
        }

        return $converted;
    }
}
