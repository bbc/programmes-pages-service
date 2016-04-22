<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Domain\Entity\Brand;
use BBC\ProgrammesPagesService\Domain\Entity\Series;
use BBC\ProgrammesPagesService\Domain\Entity\Episode;
use BBC\ProgrammesPagesService\Domain\Entity\Clip;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Mapper\MapperInterface;
use DateTimeImmutable;
use InvalidArgumentException;

class ProgrammeMapper implements MapperInterface
{
    protected $imageMapper;

    public function __construct(ImageMapper $imageMapper)
    {
        $this->imageMapper = $imageMapper;
    }

    public function getDomainModel(array $dbProgramme): Programme
    {
        if (array_key_exists('type', $dbProgramme)) {
            if ($dbProgramme['type'] == 'brand') {
                return $this->getBrandModel($dbProgramme);
            }
            if ($dbProgramme['type'] == 'series') {
                return $this->getSeriesModel($dbProgramme);
            }
            if ($dbProgramme['type'] == 'episode') {
                return $this->getEpisodeModel($dbProgramme);
            }
            if ($dbProgramme['type'] == 'clip') {
                return $this->getClipModel($dbProgramme);
            }
        }

        throw new InvalidArgumentException('Could not find build domain model for unknown programme type "' . ($dbProgramme['type'] ?? '') . '"');
    }

    private function getBrandModel(array $dbProgramme): Brand
    {
        return new Brand(
            new Pid($dbProgramme['pid']),
            $dbProgramme['title'],
            $dbProgramme['searchTitle'],
            $dbProgramme['shortSynopsis'],
            $this->getLongestSynopsis($dbProgramme),
            $this->getImageModel($dbProgramme),
            $dbProgramme['promotionsCount'],
            $dbProgramme['relatedLinksCount'],
            $dbProgramme['hasSupportingContent'],
            $dbProgramme['streamable'],
            $dbProgramme['aggregatedBroadcastsCount'],
            $dbProgramme['aggregatedEpisodesCount'],
            $dbProgramme['availableClipsCount'],
            $dbProgramme['availableEpisodesCount'],
            $dbProgramme['availableGalleriesCount'],
            $dbProgramme['isPodcastable'],
            $this->getParentModel($dbProgramme),
            $dbProgramme['releaseDate'],
            $dbProgramme['position'],
            $this->getMasterBrandModel($dbProgramme),
            $dbProgramme['expectedChildCount']
        );
    }

    private function getSeriesModel(array $dbProgramme): Series
    {
        return new Series(
            new Pid($dbProgramme['pid']),
            $dbProgramme['title'],
            $dbProgramme['searchTitle'],
            $dbProgramme['shortSynopsis'],
            $this->getLongestSynopsis($dbProgramme),
            $this->getImageModel($dbProgramme),
            $dbProgramme['promotionsCount'],
            $dbProgramme['relatedLinksCount'],
            $dbProgramme['hasSupportingContent'],
            $dbProgramme['streamable'],
            $dbProgramme['aggregatedBroadcastsCount'],
            $dbProgramme['aggregatedEpisodesCount'],
            $dbProgramme['availableClipsCount'],
            $dbProgramme['availableEpisodesCount'],
            $dbProgramme['availableGalleriesCount'],
            $dbProgramme['isPodcastable'],
            $this->getParentModel($dbProgramme),
            $dbProgramme['releaseDate'],
            $dbProgramme['position'],
            $this->getMasterBrandModel($dbProgramme),
            $dbProgramme['expectedChildCount']
        );
    }

    private function getEpisodeModel(array $dbProgramme): Episode
    {
        return new Episode(
            new Pid($dbProgramme['pid']),
            $dbProgramme['title'],
            $dbProgramme['searchTitle'],
            $dbProgramme['shortSynopsis'],
            $this->getLongestSynopsis($dbProgramme),
            $this->getImageModel($dbProgramme),
            $dbProgramme['promotionsCount'],
            $dbProgramme['relatedLinksCount'],
            $dbProgramme['hasSupportingContent'],
            $dbProgramme['streamable'],
            $dbProgramme['mediaType'],
            $dbProgramme['aggregatedBroadcastsCount'],
            $dbProgramme['availableClipsCount'],
            $dbProgramme['availableGalleriesCount'],
            $this->getParentModel($dbProgramme),
            $dbProgramme['releaseDate'],
            $dbProgramme['position'] ?? null,
            $this->getMasterBrandModel($dbProgramme),
            $dbProgramme['duration'] ?? null,
            ($dbProgramme['streamableFrom'] ? DateTimeImmutable::createFromMutable($dbProgramme['streamableFrom']) : null),
            ($dbProgramme['streamableUntil'] ? DateTimeImmutable::createFromMutable($dbProgramme['streamableUntil']) : null)
        );
    }

    private function getClipModel(array $dbProgramme): Clip
    {
        return new Clip(
            new Pid($dbProgramme['pid']),
            $dbProgramme['title'],
            $dbProgramme['searchTitle'],
            $dbProgramme['shortSynopsis'],
            $this->getLongestSynopsis($dbProgramme),
            $this->getImageModel($dbProgramme),
            $dbProgramme['promotionsCount'],
            $dbProgramme['relatedLinksCount'],
            $dbProgramme['hasSupportingContent'],
            $dbProgramme['streamable'],
            $dbProgramme['mediaType'],
            $this->getParentModel($dbProgramme),
            $dbProgramme['releaseDate'],
            $dbProgramme['position'] ?? null,
            $this->getMasterBrandModel($dbProgramme),
            $dbProgramme['duration'] ?? null,
            ($dbProgramme['streamableFrom'] ? DateTimeImmutable::createFromMutable($dbProgramme['streamableFrom']) : null),
            ($dbProgramme['streamableUntil'] ? DateTimeImmutable::createFromMutable($dbProgramme['streamableUntil']) : null)
        );
    }

    private function getParentModel($dbProgramme, $key = 'parent')
    {
        if (!array_key_exists($key, $dbProgramme) || is_null($dbProgramme[$key])) {
            return null;
        }

        return $this->getDomainModel($dbProgramme['parent']);
    }

    private function getImageModel($dbProgramme, $key = 'image')
    {
        if (!array_key_exists($key, $dbProgramme) || is_null($dbProgramme[$key])) {
            // TODO Build inheritance hierarchy

            // Use default Image
            return $this->imageMapper->getDefaultImage();
        }

        return $this->imageMapper->getDomainModel($dbProgramme[$key]);
    }

    private function getMasterBrandModel($dbProgramme, $key = 'masterBrand')
    {
        // TODO
        return null;
    }

    private function getLongestSynopsis($dbProgramme): string
    {
        if (!empty($dbProgramme['longSynopsis'])) {
            return $dbProgramme['longSynopsis'];
        }
        if (!empty($dbProgramme['mediumSynopsis'])) {
            return $dbProgramme['mediumSynopsis'];
        }
        return $dbProgramme['shortSynopsis'];
    }
}
