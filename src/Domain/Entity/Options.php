<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Map\ContactMediaMap;

class Options
{
    private $options;

    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    public function getOption(string $key)
    {
        return $this->options[$key] ?? null;
    }

    public function getContactMediaMap(): ?ContactMediaMap
    {
        return $this->options['contact_details'] ?? null;
    }
}
