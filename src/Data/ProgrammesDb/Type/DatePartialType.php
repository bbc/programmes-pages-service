<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Type;

use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;
use Doctrine\DBAL\Types\DateType;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use InvalidArgumentException;

class DatePartialType extends DateType
{
    /**
     * @var string
     */
    const DATE_PARTIAL = 'date_partial';

    public function getName(): string
    {
        return self::DATE_PARTIAL;
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return string
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return $value;
        }

        if ($value instanceof PartialDate) {
            return $value->formatMysql();
        }

        throw ConversionException::conversionFailed($value, $this->getName());
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return PartialDate|mixed
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value instanceof PartialDate) {
            return $value;
        }
        try {
            $values = array_map(function ($datePart) {
                return (int) $datePart;
            }, explode('-', $value));
            return new PartialDate(...$values);
        } catch (InvalidArgumentException $e) {
            throw ConversionException::conversionFailedFormat($value, $this->getName(), $platform->getDateFormatString());
        }
    }

    /**
     * @inheritdoc
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}
