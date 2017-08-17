<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\MusicSegment;
use BBC\ProgrammesPagesService\Domain\Entity\Segment;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Mapper\Traits\SynopsesTrait;

class SegmentMapper extends AbstractMapper
{
    use SynopsesTrait;

    const MUSIC_TYPES = ['music', 'classical'];

    private $cache = [];

    public function getCacheKey(array $dbSegment): string
    {
        return $this->buildCacheKey($dbSegment, 'id', [], [
            'contributions' => 'Contribution',
        ]);
    }

    public function getDomainModel(array $dbSegment): Segment
    {
        $cacheKey = $this->getCacheKey($dbSegment);

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

    private function getContributions(array $dbSegment): ?array
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
