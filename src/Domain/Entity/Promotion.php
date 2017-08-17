<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;

class Promotion
{
    /** @var  bool */
    private $isSuperPromotion;

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
        Pid $pid,
        PromotableInterface $promotedEntity,
        string $title,
        Synopses $synopses,
        string $url,
        int $weighting,
        bool $isSuperPromotion
    ) {
        $this->pid = $pid;
        $this->promotedEntity = $promotedEntity;
        $this->synopses = $synopses;
        $this->title = $title;
        $this->url = $url;
        $this->weighting = $weighting;
        $this->isSuperPromotion = $isSuperPromotion;
    }

    public function getIsSuperPromotion(): bool
    {
        return $this->isSuperPromotion;
    }

    public function getPid(): Pid
    {
        return $this->pid;
    }

    public function getPromotedEntity(): PromotableInterface
    {
        return $this->promotedEntity;
    }

    public function getSynopses(): Synopses
    {
        return $this->synopses;
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
