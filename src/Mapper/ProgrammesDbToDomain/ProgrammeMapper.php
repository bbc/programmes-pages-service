<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Domain\Entity\Brand;
use BBC\ProgrammesPagesService\Domain\Entity\Series;
use BBC\ProgrammesPagesService\Domain\Entity\Episode;
use BBC\ProgrammesPagesService\Domain\Entity\Clip;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;
use DateTimeImmutable;
use InvalidArgumentException;

class ProgrammeMapper extends AbstractMapper
{
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
            $dbProgramme['id'],
            new Pid($dbProgramme['pid']),
            $dbProgramme['title'],
            $dbProgramme['searchTitle'],
            $this->getSynopses($dbProgramme),
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
            $dbProgramme['position'],
            $this->getMasterBrandModel($dbProgramme),
            $this->getCategoriesModels('genre', $dbProgramme, 'categories'),
            $this->getCategoriesModels('format', $dbProgramme, 'categories'),
            ($dbProgramme['firstBroadcastDate'] ? DateTimeImmutable::createFromMutable($dbProgramme['firstBroadcastDate']) : null),
            $dbProgramme['expectedChildCount']
        );
    }

    private function getSeriesModel(array $dbProgramme): Series
    {
        return new Series(
            $dbProgramme['id'],
            new Pid($dbProgramme['pid']),
            $dbProgramme['title'],
            $dbProgramme['searchTitle'],
            $this->getSynopses($dbProgramme),
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
            $dbProgramme['position'],
            $this->getMasterBrandModel($dbProgramme),
            $this->getCategoriesModels('genre', $dbProgramme, 'categories'),
            $this->getCategoriesModels('format', $dbProgramme, 'categories'),
            ($dbProgramme['firstBroadcastDate'] ? DateTimeImmutable::createFromMutable($dbProgramme['firstBroadcastDate']) : null),
            $dbProgramme['expectedChildCount']
        );
    }

    private function getEpisodeModel(array $dbProgramme): Episode
    {
        return new Episode(
            $dbProgramme['id'],
            new Pid($dbProgramme['pid']),
            $dbProgramme['title'],
            $dbProgramme['searchTitle'],
            $this->getSynopses($dbProgramme),
            $this->getImageModel($dbProgramme),
            $dbProgramme['promotionsCount'],
            $dbProgramme['relatedLinksCount'],
            $dbProgramme['hasSupportingContent'],
            $dbProgramme['streamable'],
            $dbProgramme['mediaType'],
            $dbProgramme['segmentEventCount'],
            $dbProgramme['aggregatedBroadcastsCount'],
            $dbProgramme['availableClipsCount'],
            $dbProgramme['availableGalleriesCount'],
            $this->getParentModel($dbProgramme),
            $dbProgramme['position'] ?? null,
            $this->getMasterBrandModel($dbProgramme),
            $this->getCategoriesModels('genre', $dbProgramme, 'categories'),
            $this->getCategoriesModels('format', $dbProgramme, 'categories'),
            ($dbProgramme['firstBroadcastDate'] ? DateTimeImmutable::createFromMutable($dbProgramme['firstBroadcastDate']) : null),
            $dbProgramme['releaseDate'],
            $dbProgramme['duration'] ?? null,
            ($dbProgramme['streamableFrom'] ? DateTimeImmutable::createFromMutable($dbProgramme['streamableFrom']) : null),
            ($dbProgramme['streamableUntil'] ? DateTimeImmutable::createFromMutable($dbProgramme['streamableUntil']) : null)
        );
    }

    private function getClipModel(array $dbProgramme): Clip
    {
        return new Clip(
            $dbProgramme['id'],
            new Pid($dbProgramme['pid']),
            $dbProgramme['title'],
            $dbProgramme['searchTitle'],
            $this->getSynopses($dbProgramme),
            $this->getImageModel($dbProgramme),
            $dbProgramme['promotionsCount'],
            $dbProgramme['relatedLinksCount'],
            $dbProgramme['hasSupportingContent'],
            $dbProgramme['streamable'],
            $dbProgramme['mediaType'],
            $dbProgramme['segmentEventCount'],
            $this->getParentModel($dbProgramme),
            $dbProgramme['position'] ?? null,
            $this->getMasterBrandModel($dbProgramme),
            $this->getCategoriesModels('genre', $dbProgramme, 'categories'),
            $this->getCategoriesModels('format', $dbProgramme, 'categories'),
            ($dbProgramme['firstBroadcastDate'] ? DateTimeImmutable::createFromMutable($dbProgramme['firstBroadcastDate']) : null),
            $dbProgramme['releaseDate'],
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
        $imageMapper = $this->mapperFactory->getImageMapper();

        // Image inheritance. If the current programme does not have an image
        // attached to it, look to see if its parent has an image, and use that.
        // Keep going up the ancestry chain till an image is found, otherwise
        // fall back to a default image.
        $currentItem = $dbProgramme;
        while ($currentItem) {
            // If the current Programme has an image then use that!
            if (isset($currentItem[$key])) {
                return $imageMapper->getDomainModel($currentItem[$key]);
            }

            // Otherwise set the current Programme to the parent
            $currentItem = $currentItem['parent'] ?? null;
        }

        // Couldn't find anything in the hierarchy, try the MasterBrand image
        if (isset($dbProgramme['masterBrand']['image'])) {
            return $imageMapper->getDomainModel($dbProgramme['masterBrand']['image']);
        }

        // Couldn't find anything in the masterbrand, so use the default Image
        return $imageMapper->getDefaultImage();
    }

    private function getMasterBrandModel($dbProgramme, $key = 'masterBrand')
    {
        if (!array_key_exists($key, $dbProgramme) || is_null($dbProgramme[$key])) {
            // MasterBrand may be null if not requested or a Programme has no
            // MasterBrand attached to it
            return null;
        }

        return $this->mapperFactory->getMasterBrandMapper()->getDomainModel($dbProgramme[$key]);
    }

    private function getCategoriesModels($filterType, $dbProgramme, $key = 'categories'): array
    {
        if (!array_key_exists($key, $dbProgramme) || is_null($dbProgramme[$key])) {
            return [];
        }

        $categoryMapper = $this->mapperFactory->getCategoryMapper();
        $categories = [];
        foreach ($dbProgramme[$key] as $dbCategory) {
            if (array_key_exists('type', $dbCategory) && $dbCategory['type'] == $filterType) {
                $categories[] = $categoryMapper->getDomainModel($dbCategory);
            }
        }

        return $categories;
    }

    private function getSynopses($dbProgramme): Synopses
    {
        return new Synopses(
            $dbProgramme['shortSynopsis'],
            $dbProgramme['mediumSynopsis'],
            $dbProgramme['longSynopsis']
        );
    }
}
