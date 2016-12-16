<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\MusicSegment;
use BBC\ProgrammesPagesService\Domain\Entity\Segment;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;

class SegmentMapper extends AbstractMapper
{
    const MUSIC_TYPES = ['music', 'classical'];

    private $cache = [];

    public function getDomainModel(array $dbSegment): Segment
    {
        $cacheKey = $dbSegment['id'];

        if (!isset($this->cache[$cacheKey])) {
            if (in_array($dbSegment['type'], self::MUSIC_TYPES)) {
                $this->cache[$cacheKey] = $this->getMusicSegmentModel($dbSegment);
            } else {
                $this->cache[$cacheKey] = $this->getSegmentModel($dbSegment);
            }
        }

        return $this->cache[$cacheKey];
    }

    private function getSegmentModel(array $dbSegment): Segment
    {
        return new Segment(
            $dbSegment['id'],
            new Pid($dbSegment['pid']),
            $dbSegment['type'],
            $this->getSynopses($dbSegment),
            $dbSegment['contributionsCount'],
            $dbSegment['title'],
            $dbSegment['duration'],
            $this->getContributions($dbSegment)
        );
    }

    private function getMusicSegmentModel(array $dbSegment): MusicSegment
    {
        return new MusicSegment(
            $dbSegment['id'],
            new Pid($dbSegment['pid']),
            $dbSegment['type'],
            $this->getSynopses($dbSegment),
            $dbSegment['contributionsCount'],
            $dbSegment['title'],
            $dbSegment['duration'],
            $this->getContributions($dbSegment),
            $dbSegment['musicRecordId'],
            $dbSegment['releaseTitle'],
            $dbSegment['catalogueNumber'],
            $dbSegment['recordLabel'],
            $dbSegment['publisher'],
            $dbSegment['trackNumber'],
            $dbSegment['trackSide'],
            $dbSegment['sourceMedia'],
            $dbSegment['musicCode'],
            $dbSegment['recordingDate']
        );
    }

    private function getSynopses($dbSegment): Synopses
    {
        return new Synopses(
            $dbSegment['shortSynopsis'],
            $dbSegment['mediumSynopsis'],
            $dbSegment['longSynopsis']
        );
    }

    /**
     * @param $dbSegment
     * @return array|null
     */
    private function getContributions($dbSegment)
    {
        $contributors = [];

        if (!isset($dbSegment['contributions'])) {
            return null;
        }

        foreach ($dbSegment['contributions'] as $contribution) {
            $contributors[] =
                $this->mapperFactory->getContributionMapper()->getDomainModel($contribution);
        }

        return $contributors;
    }
}
