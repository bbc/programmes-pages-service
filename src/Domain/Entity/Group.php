<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedMasterBrand;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedOptions;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedProgramme;
use BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;
use InvalidArgumentException;

abstract class Group
{
    /** @var int[] */
    private $dbAncestryIds;

    /** @var Pid */
    private $pid;

    /** @var string */
    private $title;

    /** @var string */
    private $searchTitle;

    /** @var Synopses */
    private $synopses;

    /** @var Image */
    private $image;

    /** @var int */
    private $promotionsCount;

    /** @var int */
    private $relatedLinksCount;

    /** @var int */
    private $contributionsCount;

    /** @var Options */
    private $options;

    /** @var Programme|null */
    private $parent;

    /** @var MasterBrand|null */
    private $masterBrand;

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
        ?Programme $parent = null,
        ?MasterBrand $masterBrand = null
    ) {
        $this->assertAncestry($dbAncestryIds);

        $this->dbAncestryIds = $dbAncestryIds;
        $this->pid = $pid;
        $this->title = $title;
        $this->searchTitle = $searchTitle;
        $this->synopses = $synopses;
        $this->image = $image;
        $this->promotionsCount = $promotionsCount;
        $this->relatedLinksCount = $relatedLinksCount;
        $this->options = $options;
        $this->parent = $parent;
        $this->masterBrand = $masterBrand;
        $this->contributionsCount = $contributionsCount;
    }

    /**
     * Database ID. Yes, this is a leaky abstraction as Database Ids are
     * implementation details of how we're storing data, rather than anything
     * intrinsic to a PIPS entity. However if we keep it pure then when we look
     * up things like "All related links that belong to a Programme" then we
     * have to use the Programme PID as the key, which requires a join to the
     * CoreEntity table. This join can be avoided if we already know the Foreign
     * Key value on the Related Links table (i.e. the Programme ID field).
     * Removing these joins shall result in faster DB queries which is more
     * important than keeping a pure Domain model.
     */
    public function getDbId(): int
    {
        return end($this->dbAncestryIds);
    }

    /**
     * Database Ancestry IDs. Yes, this is a leaky abstraction (see above).
     * However it is useful to know the full ancestry if we want to make queries
     * searching through all descendants like "All Clips underneath a Brand at
     * any level, not just immediate children". This saves joining the
     * CoreEntity table to itself which is expensive.
     *
     * @return int[]
     */
    public function getDbAncestryIds(): array
    {
        return $this->dbAncestryIds;
    }

    public function getPid(): Pid
    {
        return $this->pid;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSearchTitle(): string
    {
        return $this->searchTitle;
    }

    public function getSynopses(): Synopses
    {
        return $this->synopses;
    }

    public function getShortSynopsis(): string
    {
        return $this->synopses->getShortSynopsis();
    }

    public function getLongestSynopsis(): string
    {
        return $this->synopses->getLongestSynopsis();
    }

    public function getImage(): Image
    {
        return $this->image;
    }

    public function getPromotionsCount(): int
    {
        return $this->promotionsCount;
    }

    public function getRelatedLinksCount(): int
    {
        return $this->relatedLinksCount;
    }

    public function getContributionsCount(): int
    {
        return $this->contributionsCount;
    }

    /**
     * @throws DataNotFetchedException
     */
    public function getOptions(): Options
    {
        if ($this->options instanceof UnfetchedOptions) {
            throw new DataNotFetchedException(
                'Could not get options of Group "' . $this->pid . '"' .
                ' as the full hierarchy was not fetched'
            );
        }
        return $this->options;
    }

    /**
     * @throws DataNotFetchedException
     */
    public function getOption(string $key)
    {
        return $this->getOptions()->getOption($key);
    }

    /**
     * @throws DataNotFetchedException
     */
    public function getParent(): ?Programme
    {
        if ($this->parent instanceof UnfetchedProgramme) {
            throw new DataNotFetchedException(
                'Could not get Parent of Programme "' . $this->pid . '" as it was not fetched'
            );
        }

        return $this->parent;
    }

    /**
     * @throws DataNotFetchedException
     */
    public function getMasterBrand(): ?MasterBrand
    {
        if ($this->masterBrand instanceof UnfetchedMasterBrand) {
            throw new DataNotFetchedException(
                'Could not get MasterBrand of Group "' . $this->pid . '" as it was not fetched'
            );
        }
        return $this->masterBrand;
    }

    public function getNetwork(): ?Network
    {
        return $this->masterBrand ? $this->masterBrand->getNetwork() : null;
    }

    public function getTleo(): Group
    {
        $parent = $this->getParent();
        if ($parent) {
            return $parent->getTleo();
        }
        return $this;
    }

    public function isRadio(): bool
    {
        $network = $this->getNetwork();
        return ($network && $network->isRadio());
    }

    public function isTv(): bool
    {
        $network = $this->getNetwork();
        return ($network && $network->isTv());
    }

    /**
     * @param Programme|null $context
     * @return Programme[]
     */
    public function getAncestry(?Programme $context = null): array
    {
        $currentProgramme = $this;
        $contextPid = $context ? ((string) $context->getPid()) : null;
        $ancestry = [];
        do {
            if ((string) $currentProgramme->getPid() === $contextPid) {
                break;
            }
            $ancestry[] = $currentProgramme;
        } while ($currentProgramme = $currentProgramme->getParent());

        // Make sure we never return an empty array, even if for some bizarre reason we are our own context
        return !empty($ancestry) ? $ancestry : [$this];
    }

    /**
     * @throws InvalidArgumentException
     */
    private function assertAncestry(array $array): void
    {
        if (empty($array)) {
            throw new InvalidArgumentException('Tried to create a Group with invalid ancestry. Expected a non-empty array of integers but the array was empty');
        }

        foreach ($array as $item) {
            if (!is_int($item)) {
                throw new InvalidArgumentException(sprintf(
                    'Tried to create a Group with invalid ancestry. Expected a non-empty array of integers but the array contained an instance of "%s"',
                    (is_object($item) ? get_class($item) : gettype($item))
                ));
            }
        }
    }
}
