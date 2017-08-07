<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;

class Collection extends Group
{
    const TYPE = 'collection';

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
        Options $options,
        bool $isPodcastable,
        ?MasterBrand $masterBrand = null,
        ?Programme $parent = null
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
            $masterBrand
        );

        $this->isPodcastable = $isPodcastable;
        $this->parent = $parent;
    }

    public function isPodcastable(): bool
    {
        return $this->isPodcastable;
    }
}
