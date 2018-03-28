<?php

namespace BBC\ProgrammesPagesService\Domain\ValueObject;

class Synopses
{
    private $shortestSynopsis;

    private $shortSynopsis;

    private $mediumSynopsis;

    private $longSynopsis;

    private $longestSynopsis;

    public function __construct(
        string $shortSynopsis,
        string $mediumSynopsis = '',
        string $longSynopsis = ''
    ) {
        $this->shortSynopsis = $shortSynopsis;
        $this->mediumSynopsis = $mediumSynopsis;
        $this->longSynopsis = $longSynopsis;

        if (!empty($longSynopsis)) {
            $this->longestSynopsis = $longSynopsis;
        } elseif (!empty($mediumSynopsis)) {
            $this->longestSynopsis = $mediumSynopsis;
        } else {
            $this->longestSynopsis = $shortSynopsis;
        }

        if (!empty($shortSynopsis)) {
            $this->shortestSynopsis = $shortSynopsis;
        } elseif (!empty($mediumSynopsis)) {
            $this->shortestSynopsis = $mediumSynopsis;
        } else {
            $this->shortestSynopsis = $longSynopsis;
        }
    }

    public function getShortestSynopsis(): string
    {
        return $this->shortestSynopsis;
    }

    public function getShortSynopsis(): string
    {
        return $this->shortSynopsis;
    }

    public function getMediumSynopsis(): string
    {
        return $this->mediumSynopsis;
    }

    public function getLongSynopsis(): string
    {
        return $this->longSynopsis;
    }

    public function getLongestSynopsis(): string
    {
        return $this->longestSynopsis;
    }
}
