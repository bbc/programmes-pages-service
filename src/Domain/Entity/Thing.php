<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

class Thing
{
    /** @var string */
    private $id;

    /** @var string */
    private $preferredLabel;

    /** @var string|null */
    private $disambiguationHint;

    public function __construct(string $id, string $preferredLabel, ?string $disambiguationHint)
    {
        $this->id = $id;
        $this->preferredLabel = $preferredLabel;
        $this->disambiguationHint = $disambiguationHint;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPreferredLabel(): string
    {
        return $this->preferredLabel;
    }

    public function getDisambiguationHint(): ?string
    {
        return $this->disambiguationHint;
    }
}
