<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

class Format
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $urlKey;

    public function __construct(
        string $id,
        string $title,
        string $urlKey
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->urlKey = $urlKey;
    }

    public function getId(): string
    {
        return $this->id;
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
