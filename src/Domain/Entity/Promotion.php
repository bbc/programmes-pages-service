<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

class Promotion
{
    /**
     * Is the promotion propagated down in the hierarchy?
     *
     * @var bool
     */
    private $cascadesToDescendants;

    /**
     * entity showing the promotion
     *
     * @var CoreEntity
     */
    private $context;

    /** @var string  */
    private $title;

    /** @var string  */
    private $url;

    /**
     * Promotions are sorted by weights
     *
     * @var int
     */
    private $weighting;

    public function __construct(
        bool $cascadesToDescendants,
        CoreEntity $context,
        string $title,
        string $url,
        int $weighting
    ) {
        $this->cascadesToDescendants = $cascadesToDescendants;
        $this->context = $context;
        $this->title = $title;
        $this->url = $url;
        $this->weighting = $weighting;
    }

    public function isCascadesToDescendants(): bool
    {
        return $this->cascadesToDescendants;
    }

    public function setCascadesToDescendants(bool $cascadesToDescendants)
    {
        $this->cascadesToDescendants = $cascadesToDescendants;
    }

    public function getContext(): CoreEntity
    {
        return $this->context;
    }

    public function setContext(CoreEntity $context)
    {
        $this->context = $context;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url)
    {
        $this->url = $url;
    }

    public function getWeighting(): int
    {
        return $this->weighting;
    }

    public function setWeighting(int $weighting)
    {
        $this->weighting = $weighting;
    }


}
