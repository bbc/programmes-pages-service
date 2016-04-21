<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

class VersionType
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $name;

    public function __construct(string $type, string $name)
    {
        $this->type = $type;
        $this->name = $name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
