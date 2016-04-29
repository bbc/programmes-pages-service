<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

class Format
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $urlKey;

    public function __construct(
        string $title,
        string $urlKey
    ) {
        $this->title = $title;
        $this->urlKey = $urlKey;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getUrlKey(): string
    {
        return $this->urlKey;
    }
}