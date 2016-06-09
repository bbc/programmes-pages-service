<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

class Genre
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

    /**
     * @var Genre|null
     */
    private $parent;

    public function __construct(
        string $id,
        string $title,
        string $urlKey,
        Genre $parent = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->urlKey = $urlKey;
        $this->parent = $parent;
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

    /**
     * @return Genre|null
     */
    public function getParent()
    {
        return $this->parent;
    }
}
