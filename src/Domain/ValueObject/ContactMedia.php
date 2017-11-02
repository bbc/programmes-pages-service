<?php

namespace BBC\ProgrammesPagesService\Domain\ValueObject;

use BBC\ProgrammesPagesService\Domain\Enumeration\ContactMediaEnum;
use InvalidArgumentException;

class ContactMedia
{
    /**
     * @see ContactMediaEnum::VALID_MEDIA
     *
     * @var string
     */
    private $type;

    /**
     * Examples:
     * Any email, mobile phone, postcode, reddit id, facebook, ...
     *
     * @var string
     */
    private $value;

    /** @var string */
    private $freetext;

    public function __construct(string $mediaType, string $value, string $freetext)
    {
        $this->type = $mediaType;
        $this->value = $value;
        $this->freetext = $freetext;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getFreetext(): string
    {
        return $this->freetext;
    }
}
