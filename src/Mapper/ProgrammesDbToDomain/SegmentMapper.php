<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\MapperInterface;
use BBC\ProgrammesPagesService\Domain\Entity\MusicSegment;
use BBC\ProgrammesPagesService\Domain\Entity\Segment;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;

class SegmentMapper implements MapperInterface
{
    const MUSIC_TYPES = ['music', 'classical'];

    public function getDomainModel(array $dbSegment): Segment
    {
        if (in_array($dbSegment['type'], self::MUSIC_TYPES)) {
            return $this->getMusicSegmentModel($dbSegment);
        }

        return $this->getSegmentModel($dbSegment);
    }

    private function getSegmentModel(array $dbSegment): Segment
    {
        return new Segment(
            $dbSegment['id'],
            new Pid($dbSegment['pid']),
            $dbSegment['type'],
            $dbSegment['title'],
            $this->getSynopses($dbSegment),
            $dbSegment['duration']
        );
    }

    private function getMusicSegmentModel(array $dbSegment): MusicSegment
    {
        return new MusicSegment(
            $dbSegment['id'],
            new Pid($dbSegment['pid']),
            $dbSegment['type'],
            $dbSegment['title'],
            $this->getSynopses($dbSegment),
            $dbSegment['duration'],
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
}
