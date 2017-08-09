<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;

class Promotion
{
    /** @var bool */
    private $cascadesToDescendants;

    /** @var Pid */
    private $pid;

    /** @var PromotableInterface */
    private $promotedEntity;

    /** @var Synopses */
    private $synopses;

    /** @var string */
    private $title;

    /** @var string */
    private $url;

    /** @var int */
    private $weighting;

    public function __construct(
        bool $cascadesToDescendants,
        Pid $pid,
        PromotableInterface $promotedEntity,
        Synopses $synopses,
        string $title,
        string $url,
        int $weighting
    ) {
        $this->cascadesToDescendants = $cascadesToDescendants;
        $this->pid = $pid;
        $this->promotedEntity = $promotedEntity;
        $this->synopses = $synopses;
        $this->title = $title;
        $this->url = $url;
        $this->weighting = $weighting;
    }

    public function isCascadesToDescendants(): bool
    {
        return $this->cascadesToDescendants;
    }

    public function getPid(): Pid
    {
        return $this->pid;
    }

    public function getPromotedEntity(): PromotableInterface
    {
        return $this->promotedEntity;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getWeighting(): int
    {
        return $this->weighting;
    }
}
