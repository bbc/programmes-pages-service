<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Type;

use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateType;
use InvalidArgumentException;
use TypeError;

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
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
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
     * @return PartialDate|null
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?PartialDate
    {
        if ($value === null || $value instanceof PartialDate) {
            return $value;
        }
        try {
            $values = explode('-', $value);
            return new PartialDate(...$values);
        } catch (TypeError | InvalidArgumentException $e) {
            throw ConversionException::conversionFailedFormat($value, $this->getName(), $platform->getDateFormatString());
        }
    }

    /**
     * @inheritdoc
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
