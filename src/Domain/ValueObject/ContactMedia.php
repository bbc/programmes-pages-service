<?php

namespace BBC\ProgrammesPagesService\Domain\ValueObject;

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

    public function getValue()
    {
        return $this->value;
    }

    public function getFreeText()
    {
        return $this->freeText;
    }

    public function getType()
    {
        return $this->type;
    }
}
