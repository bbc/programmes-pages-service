<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;

class Collection extends Group
{
    /* @var bool */
    private $isPodcastable;

    public function __construct(
        array $dbAncestryIds,
        Pid $pid,
        string $title,
        string $searchTitle,
        Synopses $synopses,
        Image $image,
        int $promotionsCount,
        int $relatedLinksCount,
        int $contributionsCount,
        bool $isPodcastable,
        Options $options,
        ?Programme $parent = null,
        ?MasterBrand $masterBrand = null
    ) {
        parent::__construct(
            $dbAncestryIds,
            $pid,
            $title,
            $searchTitle,
            $synopses,
            $image,
            $promotionsCount,
            $relatedLinksCount,
            $contributionsCount,
            $options,
            $parent,
            $masterBrand
        );

        $this->isPodcastable = $isPodcastable;
    }

    public function isPodcastable(): bool
    {
        return $this->isPodcastable;
    }
}
