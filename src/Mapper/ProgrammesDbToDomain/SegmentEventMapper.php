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
    public function getDomainModel(array $dbSegmentEvent): SegmentEvent
    {
        return new SegmentEvent(
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

    private function getVersionModel($dbSegmentEvent, $key = 'version'): Version
    {
        if (!array_key_exists($key, $dbSegmentEvent) || is_null($dbSegmentEvent[$key])) {
            return new UnfetchedVersion();
        }

        return $this->mapperFactory->getVersionMapper()->getDomainModel($dbSegmentEvent[$key]);
    }

    private function getSegmentModel($dbSegmentEvent, $key = 'segment'): Segment
    {
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
