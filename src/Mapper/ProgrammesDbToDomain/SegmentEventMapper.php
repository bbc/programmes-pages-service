<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\SegmentEvent;
use BBC\ProgrammesPagesService\Domain\Entity\Segment;
use BBC\ProgrammesPagesService\Domain\Entity\Version;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedSegment;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedVersion;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;

class SegmentEventMapper extends AbstractMapper
{
    private $cache = [];

    public function getDomainModel(array $dbSegmentEvent): SegmentEvent
    {
        $cacheKey = $dbSegmentEvent['id'];

        if (!isset($this->cache[$cacheKey])) {
            $this->cache[$cacheKey] = new SegmentEvent(
                new Pid($dbSegmentEvent['pid']),
                $this->getVersionModel($dbSegmentEvent),
                $this->getSegmentModel($dbSegmentEvent),
                $this->getSynopses($dbSegmentEvent),
                $dbSegmentEvent['title'],
                $dbSegmentEvent['isChapter'],
                $dbSegmentEvent['offset'],
                $dbSegmentEvent['position']
            );
        }

        return $this->cache[$cacheKey];
    }

    private function getVersionModel($dbSegmentEvent, $key = 'version'): Version
    {
        // It is not valid for a SegmentEvent to have no version
        // so it counts as Unfetched even if the key exists but is null
        if (!array_key_exists($key, $dbSegmentEvent) || is_null($dbSegmentEvent[$key])) {
            return new UnfetchedVersion();
        }

        return $this->mapperFactory->getVersionMapper()->getDomainModel($dbSegmentEvent[$key]);
    }

    private function getSegmentModel($dbSegmentEvent, $key = 'segment'): Segment
    {
        // It is not valid for a SegmentEvent to have no segment
        // so it counts as Unfetched even if the key exists but is null
        if (!array_key_exists($key, $dbSegmentEvent) || is_null($dbSegmentEvent[$key])) {
            return new UnfetchedSegment();
        }

        return $this->mapperFactory->getSegmentMapper()->getDomainModel($dbSegmentEvent[$key]);
    }

    private function getSynopses($dbSegmentEvent): Synopses
    {
        return new Synopses(
            $dbSegmentEvent['shortSynopsis'],
            $dbSegmentEvent['mediumSynopsis'],
            $dbSegmentEvent['longSynopsis']
        );
    }
}
