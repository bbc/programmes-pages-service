<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

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
}
