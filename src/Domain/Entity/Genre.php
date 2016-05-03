<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

class Genre
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $urlKey;

    /**
     * @var Genre|null
     */
    private $parent;

    public function __construct(
        string $title,
        string $urlKey,
        Genre $parent = null
    ) {
        $this->title = $title;
        $this->urlKey = $urlKey;
        $this->parent = $parent;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getUrlKey(): string
    {
        return $this->urlKey;
    }

    /**
     * @return Genre|null
     */
    public function getParent()
    {
        return $this->parent;
    }
}
