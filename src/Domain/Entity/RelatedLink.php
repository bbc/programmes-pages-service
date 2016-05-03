<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

class RelatedLink
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $uri;

     /**
     * @var string
     */
    private $shortSynopsis;

     /**
     * @var string
     */
    private $longestSynopsis;

    /**
     * @var string
     */
    private $type;

    /**
     * @var bool
     */
    private $isExternal;

    public function __construct(
        string $title,
        string $uri,
        string $shortSynopsis,
        string $longestSynopsis,
        string $type,
        bool $isExternal
    ) {
        $this->title = $title;
        $this->uri = $uri;
        $this->shortSynopsis = $shortSynopsis;
        $this->longestSynopsis = $longestSynopsis;
        $this->type = $type;
        $this->isExternal = $isExternal;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getShortSynopsis(): string
    {
        return $this->shortSynopsis;
    }

    public function getLongestSynopsis(): string
    {
        return $this->longestSynopsis;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isExternal(): bool
    {
        return $this->isExternal;
    }
}
