<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\Entity\MasterBrand;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Domain\Entity\Brand;
use BBC\ProgrammesPagesService\Domain\Entity\Series;
use BBC\ProgrammesPagesService\Domain\Entity\Episode;
use BBC\ProgrammesPagesService\Domain\Entity\Clip;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedProgramme;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedMasterBrand;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;
use DateTimeImmutable;
use InvalidArgumentException;

class ProgrammeMapper extends AbstractMapper
{
    private $cache = [];

    public function getCacheKey(array $dbProgramme): string
    {
        return $this->buildCacheKey($dbProgramme, 'id', [
            'image' => 'Image',
            'parent' => 'Programme',
            'masterBrand' => 'MasterBrand',
        ], [
            'categories' => 'Category',
        ]);
    }

    public function getDomainModel(array $dbProgramme): Programme
    {
        $cacheKey = $this->getCacheKey($dbProgramme);

        if (!isset($this->cache[$cacheKey])) {
            $this->cache[$cacheKey] = $this->getModel($dbProgramme);
        }

        return $this->cache[$cacheKey];
    }

    public function getModel(array $dbProgramme): Programme
    {
        if (isset($dbProgramme['type'])) {
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

        throw new InvalidArgumentException('Could not build domain model for unknown programme type "' . ($dbProgramme['type'] ?? '') . '"');
    }

    private function getBrandModel(array $dbProgramme): Brand
    {
        return new Brand(
            $this->getAncestryArray($dbProgramme),
            new Pid($dbProgramme['pid']),
            $dbProgramme['title'],
            $dbProgramme['searchTitle'],
            $this->getSynopses($dbProgramme),
            $this->getImageModel($dbProgramme),
            $dbProgramme['promotionsCount'],
            $dbProgramme['relatedLinksCount'],
            $dbProgramme['hasSupportingContent'],
            $dbProgramme['streamable'],
            $dbProgramme['streamableAlternate'],
            $dbProgramme['contributionsCount'],
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
            $this->getAncestryArray($dbProgramme),
            new Pid($dbProgramme['pid']),
            $dbProgramme['title'],
            $dbProgramme['searchTitle'],
            $this->getSynopses($dbProgramme),
            $this->getImageModel($dbProgramme),
            $dbProgramme['promotionsCount'],
            $dbProgramme['relatedLinksCount'],
            $dbProgramme['hasSupportingContent'],
            $dbProgramme['streamable'],
            $dbProgramme['streamableAlternate'],
            $dbProgramme['contributionsCount'],
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
            $this->getAncestryArray($dbProgramme),
            new Pid($dbProgramme['pid']),
            $dbProgramme['title'],
            $dbProgramme['searchTitle'],
            $this->getSynopses($dbProgramme),
            $this->getImageModel($dbProgramme),
            $dbProgramme['promotionsCount'],
            $dbProgramme['relatedLinksCount'],
            $dbProgramme['hasSupportingContent'],
            $dbProgramme['streamable'],
            $dbProgramme['streamableAlternate'],
            $dbProgramme['contributionsCount'],
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
            $this->getAncestryArray($dbProgramme),
            new Pid($dbProgramme['pid']),
            $dbProgramme['title'],
            $dbProgramme['searchTitle'],
            $this->getSynopses($dbProgramme),
            $this->getImageModel($dbProgramme),
            $dbProgramme['promotionsCount'],
            $dbProgramme['relatedLinksCount'],
            $dbProgramme['hasSupportingContent'],
            $dbProgramme['streamable'],
            $dbProgramme['streamableAlternate'],
            $dbProgramme['contributionsCount'],
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

    private function getAncestryArray(array $dbProgramme, string $key = 'ancestry'): array
    {
        // ancestry contains a string of all IDs including the current one with
        // a trailing comma at the end (which makes it an empty item when exploding)
        // Thus we want an array of all but the final item (which is null)
        $ancestors = explode(',', $dbProgramme[$key], -1) ?? [];
        return array_map(function ($a) {
            return (int) $a;
        }, $ancestors);
    }

    private function getParentModel(array $dbProgramme, string $key = 'parent'): ?Programme
    {
        // It is possible to have no parent, where the key does
        // exist but is set to null. We'll only say it's Unfetched
        // if the key doesn't exist at all.
        if (!array_key_exists($key, $dbProgramme)) {
            return new UnfetchedProgramme();
        }

        if (is_null($dbProgramme[$key])) {
            return null;
        }

        return $this->getDomainModel($dbProgramme[$key]);
    }

    private function getImageModel(array $dbProgramme, string $key = 'image'): ?Image
    {
        $imageMapper = $this->mapperFactory->getImageMapper();

        // Image inheritance. If the current programme does not have an image
        // attached to it, look to see if its parent has an image, and use that.
        // Keep going up the ancestry chain till an image is found
        $currentItem = $dbProgramme;
        while ($currentItem) {
            // If the current Programme has an image then use that!
            if (isset($currentItem[$key])) {
                return $imageMapper->getDomainModel($currentItem[$key]);
            }

            // Otherwise set the current Programme to the parent
            $currentItem = $currentItem['parent'] ?? null;
        }

        // Could not find any Programme Images in the hierarchy, try the
        // MasterBrand image.
        // This should also attempt inheritance where if the current programme
        // has no MasterBrand image then it should work up the ancestry chain
        // till an image is found.
        $currentItem = $dbProgramme;
        while ($currentItem) {
            // If the current Programme's MasterBrand has an image then use that!
            if (isset($currentItem['masterBrand']['image'])) {
                return $imageMapper->getDomainModel($currentItem['masterBrand']['image']);
            }

            // Otherwise set the current Programme to the parent
            $currentItem = $currentItem['parent'] ?? null;
        }

        // Could not find any programme image in the masterbrand, go up to the network
        $currentItem = $dbProgramme;
        while ($currentItem) {
            // If the current Programme's MasterBrand has an image then use that!
            if (isset($currentItem['masterBrand']['network']['image'])) {
                return $imageMapper->getDomainModel($currentItem['masterBrand']['network']['image']);
            }

            // Otherwise set the current Programme to the parent
            $currentItem = $currentItem['parent'] ?? null;
        }

        // Couldn't find anything in the masterbrand, so use the default Image
        return $imageMapper->getDefaultImage();
    }

    private function getMasterBrandModel(array $dbProgramme, string $key = 'masterBrand'): ?MasterBrand
    {
        // It is possible to have no MasterBrand, where the key does
        // exist but is set to null. We'll only say it's Unfetched
        // if the key doesn't exist at all.
        if (!array_key_exists($key, $dbProgramme)) {
            return new UnfetchedMasterBrand();
        }

        if (is_null($dbProgramme[$key])) {
            return null;
        }

        return $this->mapperFactory->getMasterBrandMapper()->getDomainModel($dbProgramme[$key]);
    }

    private function getCategoriesModels(string $filterType, array $dbProgramme, string $key = 'categories'): ?array
    {
        if (!isset($dbProgramme[$key]) || !is_array($dbProgramme[$key])) {
            return null;
        }

        $categoryMapper = $this->mapperFactory->getCategoryMapper();
        $categories = [];
        foreach ($dbProgramme[$key] as $dbCategory) {
            if (isset($dbCategory['type']) && $dbCategory['type'] == $filterType) {
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
