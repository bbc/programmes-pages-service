<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Type;

use Cake\Chronos\Chronos;
use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeType;
use InvalidArgumentException;

class UtcDateTimeType extends DateTimeType
{
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

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Chronos
    {
        if (is_null($value) || $value instanceof Chronos) {
            return $value;
        }

        $converted = Chronos::createFromFormat($platform->getDateTimeFormatString(), $value, 'UTC');

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
