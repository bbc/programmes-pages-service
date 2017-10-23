<?php

namespace BBC\ProgrammesPagesService\Domain\ValueObject;

use BBC\ProgrammesPagesService\Domain\Enumeration\ContactMediaEnum;

class ContactMedia
{
    private $value;
    private $freetext;
    private $type;

    public function __construct($type, $value, $freetext)
    {
        $this->type = $type;
        $this->value = $value;
        $this->freetext = $freetext;
    }

    /**
     * Examples:
     * Any email, mobile phone, postcode, reddit id, facebook, ...
     */
    public function getValue(): string
    {
        return $this->value;
    }

    public function getFreeText(): string
    {
        return $this->freeText;
    }

    /**
     * @see ContactMediaEnum::validMedia()
     */
    public function getType(): string
    {
        return $this->type;
    }
}
