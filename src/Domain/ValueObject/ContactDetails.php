<?php

namespace BBC\ProgrammesPagesService\Domain\ValueObject;

use BBC\ProgrammesPagesService\Domain\Enumeration\ContactMediumEnum;

class ContactDetails
{
    /**
     * @see ContactMediumEnum::VALID_MEDIUM
     *
     * @var string
     */
    private $type;

    /** @var string */
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
