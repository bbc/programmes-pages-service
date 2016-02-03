<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Type;

use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;
use Doctrine\DBAL\Types\DateType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

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
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return $value;
        }

        if ($value instanceof PartialDate) {
            return $value->formatMysql();
        }

        throw ConversionException::conversionFailedInvalidType($value, $this->getName(), [
            'null',
            'BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate',
        ]);
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return mixed
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value instanceof PartialDate) {
            return $value;
        }

        $val = new PartialDate($value);
        if (!$val) {
            throw ConversionException::conversionFailedFormat($value, $this->getName(), $platform->getDateFormatString());
        }

        return $val;
    }

    /**
     * @inheritdoc
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}
