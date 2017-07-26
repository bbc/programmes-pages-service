<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Brand;
use BBC\ProgrammesPagesService\Domain\Entity\Clip;
use BBC\ProgrammesPagesService\Domain\Entity\Collection;
use BBC\ProgrammesPagesService\Domain\Entity\CoreEntity;
use BBC\ProgrammesPagesService\Domain\Entity\Episode;
use BBC\ProgrammesPagesService\Domain\Entity\Franchise;
use BBC\ProgrammesPagesService\Domain\Entity\Gallery;
use BBC\ProgrammesPagesService\Domain\Entity\Group;
use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\Entity\MasterBrand;
use BBC\ProgrammesPagesService\Domain\Entity\Options;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Domain\Entity\Season;
use BBC\ProgrammesPagesService\Domain\Entity\Series;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedMasterBrand;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedOptions;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedProgramme;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;
use InvalidArgumentException;

class CoreEntityMapper extends AbstractMapper
{
    private $cache = [];

    /**
     * For building cache keys for other mappers in a generic way
     *
     * @param array $dbEntity
     * @return string
     * @throws InvalidArgumentException
     */
    public function getCacheKey(array $dbEntity): string
    {
        if ($this->entityIsA(Group::class, $dbEntity)) {
            return $this->getCacheKeyForGroup($dbEntity);
        }
        if ($this->entityIsA(Programme::class, $dbEntity)) {
            return $this->getCacheKeyForProgramme($dbEntity);
        }
        throw new InvalidArgumentException('Unrecognized Core Entity');
    }

    public function getCacheKeyForGroup(array $dbGroup): string
    {
        return $this->buildCacheKey($dbGroup, 'id', [
            'image' => 'Image',
            'parent' => 'CoreEntity',
            'masterBrand' => 'MasterBrand',
        ]);
    }

    public function getCacheKeyForProgramme(array $dbProgramme): string
    {
        return $this->buildCacheKey($dbProgramme, 'id', [
            'image' => 'Image',
            'parent' => 'CoreEntity',
            'masterBrand' => 'MasterBrand',
        ], [
            'categories' => 'Category',
        ]);
    }

    /**
     * @param array $dbEntity
     * @return CoreEntity
     * @throws InvalidArgumentException
     */
    public function getDomainModel(array $dbEntity): CoreEntity
    {
        if ($this->entityIsA(Group::class, $dbEntity)) {
            return $this->getDomainModelForGroup($dbEntity);
        }
        if ($this->entityIsA(Programme::class, $dbEntity)) {
            return $this->getDomainModelForProgramme($dbEntity);
        }
        throw new InvalidArgumentException('Unrecognized Core Entity');
    }

    public function getDomainModelForGroup(array $dbEntity): Group
    {
        if (!$this->entityIsA(Group::class, $dbEntity)) {
            throw new InvalidArgumentException('Could not build domain model for unknown group type "' . ($dbEntity['type'] ?? '') . '"');
        }

        $cacheKey = $this->getCacheKeyForGroup($dbEntity);

        if (!isset($this->cache[$cacheKey])) {
            $this->cache[$cacheKey] = $this->getModelForGroup($dbEntity);
        }

        return $this->cache[$cacheKey];
    }

    public function getDomainModelForProgramme(array $dbEntity): Programme
    {
        if (!$this->entityIsA(Programme::class, $dbEntity)) {
            throw new InvalidArgumentException('Could not build domain model for unknown programme type "' . ($dbEntity['type'] ?? '') . '"');
        }

        $cacheKey = $this->getCacheKeyForProgramme($dbEntity);

        if (!isset($this->cache[$cacheKey])) {
            $this->cache[$cacheKey] = $this->getModelForProgramme($dbEntity);
        }

        return $this->cache[$cacheKey];
    }

    public function getModelForGroup(array $dbEntity): Group
    {
        if (isset($dbEntity['type'])) {
            if ($dbEntity['type'] == 'collection') {
                return $this->getCollectionModel($dbEntity);
            }
            if ($dbEntity['type'] == 'gallery') {
                return $this->getGalleryModel($dbEntity);
            }
            if ($dbEntity['type'] == 'season') {
                return $this->getSeasonModel($dbEntity);
            }
            if ($dbEntity['type'] == 'franchise') {
                return $this->getFranchiseModel($dbEntity);
            }
        }

        throw new InvalidArgumentException('Could not build domain model for unknown group type "' . ($dbEntity['type'] ?? '') . '"');
    }

    public function getModelForProgramme(array $dbEntity): Programme
    {
        if (isset($dbEntity['type'])) {
            if ($dbEntity['type'] == 'brand') {
                return $this->getBrandModel($dbEntity);
            }
            if ($dbEntity['type'] == 'series') {
                return $this->getSeriesModel($dbEntity);
            }
            if ($dbEntity['type'] == 'episode') {
                return $this->getEpisodeModel($dbEntity);
            }
            if ($dbEntity['type'] == 'clip') {
                return $this->getClipModel($dbEntity);
            }
        }

        throw new InvalidArgumentException('Could not build domain model for unknown programme type "' . ($dbEntity['type'] ?? '') . '"');
    }

    private function getCollectionModel($dbGroup): Collection
    {
        return new Collection(
            $this->getAncestryArray($dbGroup),
            new Pid($dbGroup['pid']),
            $dbGroup['title'],
            $dbGroup['searchTitle'],
            $this->getSynopses($dbGroup),
            $this->getImageModel($dbGroup),
            $dbGroup['promotionsCount'],
            $dbGroup['relatedLinksCount'],
            $dbGroup['contributionsCount'],
            $this->getOptionsModel($dbGroup),
            $dbGroup['isPodcastable'],
            $this->getMasterBrandModel($dbGroup),
            $this->getParentModel($dbGroup)
        );
    }

    private function getFranchiseModel($dbGroup): Franchise
    {
        return new Franchise(
            $this->getAncestryArray($dbGroup),
            new Pid($dbGroup['pid']),
            $dbGroup['title'],
            $dbGroup['searchTitle'],
            $this->getSynopses($dbGroup),
            $this->getImageModel($dbGroup),
            $dbGroup['promotionsCount'],
            $dbGroup['relatedLinksCount'],
            $dbGroup['contributionsCount'],
            $this->getOptionsModel($dbGroup),
            $dbGroup['aggregatedBroadcastsCount'],
            $this->getMasterBrandModel($dbGroup)
        );
    }

    private function getGalleryModel(array $dbGroup): Gallery
    {
        return new Gallery(
            $this->getAncestryArray($dbGroup),
            new Pid($dbGroup['pid']),
            $dbGroup['title'],
            $dbGroup['searchTitle'],
            $this->getSynopses($dbGroup),
            $this->getImageModel($dbGroup),
            $dbGroup['promotionsCount'],
            $dbGroup['relatedLinksCount'],
            $dbGroup['contributionsCount'],
            $this->getOptionsModel($dbGroup),
            $this->getMasterBrandModel($dbGroup),
            $this->getParentModel($dbGroup)
        );
    }

    private function getSeasonModel($dbGroup): Season
    {
        return new Season(
            $this->getAncestryArray($dbGroup),
            new Pid($dbGroup['pid']),
            $dbGroup['title'],
            $dbGroup['searchTitle'],
            $this->getSynopses($dbGroup),
            $this->getImageModel($dbGroup),
            $dbGroup['promotionsCount'],
            $dbGroup['relatedLinksCount'],
            $dbGroup['contributionsCount'],
            $this->getOptionsModel($dbGroup),
            $dbGroup['aggregatedBroadcastsCount'],
            $this->getMasterBrandModel($dbGroup)
        );
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
            $this->getOptionsModel($dbProgramme),
            $this->getParentModel($dbProgramme),
            $dbProgramme['position'],
            $this->getMasterBrandModel($dbProgramme),
            $this->getCategoriesModels('genre', $dbProgramme, 'categories'),
            $this->getCategoriesModels('format', $dbProgramme, 'categories'),
            $this->castDateTime($dbProgramme['firstBroadcastDate']),
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
            $this->getOptionsModel($dbProgramme),
            $this->getParentModel($dbProgramme),
            $dbProgramme['position'],
            $this->getMasterBrandModel($dbProgramme),
            $this->getCategoriesModels('genre', $dbProgramme, 'categories'),
            $this->getCategoriesModels('format', $dbProgramme, 'categories'),
            $this->castDateTime($dbProgramme['firstBroadcastDate']),
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
            $this->getOptionsModel($dbProgramme),
            $this->getParentModel($dbProgramme),
            $dbProgramme['position'] ?? null,
            $this->getMasterBrandModel($dbProgramme),
            $this->getCategoriesModels('genre', $dbProgramme, 'categories'),
            $this->getCategoriesModels('format', $dbProgramme, 'categories'),
            $this->castDateTime($dbProgramme['firstBroadcastDate']),
            $dbProgramme['releaseDate'],
            $dbProgramme['duration'] ?? null,
            $this->castDateTime($dbProgramme['streamableFrom']),
            $this->castDateTime($dbProgramme['streamableUntil'])
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
            $this->getOptionsModel($dbProgramme),
            $this->getParentModel($dbProgramme),
            $dbProgramme['position'] ?? null,
            $this->getMasterBrandModel($dbProgramme),
            $this->getCategoriesModels('genre', $dbProgramme, 'categories'),
            $this->getCategoriesModels('format', $dbProgramme, 'categories'),
            $this->castDateTime($dbProgramme['firstBroadcastDate']),
            $dbProgramme['releaseDate'],
            $dbProgramme['duration'] ?? null,
            $this->castDateTime($dbProgramme['streamableFrom']),
            $this->castDateTime($dbProgramme['streamableUntil'])
        );
    }

    private function getAncestryArray(array $dbEntity, string $key = 'ancestry'): array
    {
        // ancestry contains a string of all IDs including the current one with
        // a trailing comma at the end (which makes it an empty item when exploding)
        // Thus we want an array of all but the final item (which is null)
        $ancestors = explode(',', $dbEntity[$key], -1) ?? [];
        return array_map(function ($a) {
            return (int) $a;
        }, $ancestors);
    }

    private function getOptionsModel(array $dbEntity, string $key = 'options'): Options
    {
        // ensure the full hierarchy has been fetched.
        // Options are only valid if we got everything.
        if (!$this->hasFetchedFullHierarchy($dbEntity)) {
            return new UnfetchedOptions();
        }

        // build for current dbEntity the tree of options
        $optionsTree = $this->getOptionsTree($dbEntity, $key);
        // get final options to be applied on entity from tree options hierarchy
        return $this->mapperFactory
            ->getOptionsMapper()
            ->getDomainModel(...$optionsTree);
    }

    private function hasFetchedFullHierarchy(array $programme): bool
    {
        // find the top level programme, ensuring it exists
        $tleo = $programme;
        while ($programme) {
            $tleo = $programme;
            if (!array_key_exists('parent', $programme)) {
                return false;
            }
            $programme = $programme['parent'];
        }
        // check that the masterBrand was fetched
        if (!array_key_exists('masterBrand', $tleo)) {
            return false;
        }

        // check that the network was fetched
        if ($tleo['masterBrand'] &&
            !array_key_exists('network', $tleo['masterBrand'])
        ) {
            return false;
        }

        return true;
    }

    private function getOptionsTree(array $dbEntity, string $keyWithOptions, array $optionsTree = []): array
    {
        // Recursive up the entity hierarchy, then to the network above that
        $optionsTree[] = $dbEntity[$keyWithOptions] ?? [];
        if (isset($dbEntity['parent'])) {
            $optionsTree = $this->getOptionsTree($dbEntity['parent'], $keyWithOptions, $optionsTree);
        } elseif (isset($dbEntity['masterBrand']['network'])) {
            $optionsTree = $this->getOptionsTree($dbEntity['masterBrand']['network'], $keyWithOptions, $optionsTree);
        }
        return $optionsTree;
    }

    private function getParentModel(array $dbEntity, string $key = 'parent'): ?Programme
    {
        // It is possible to have no parent, where the key does
        // exist but is set to null. We'll only say it's Unfetched
        // if the key doesn't exist at all.
        if (!array_key_exists($key, $dbEntity)) {
            return new UnfetchedProgramme();
        }

        if (is_null($dbEntity[$key])) {
            return null;
        }

        return $this->getDomainModelForProgramme($dbEntity[$key]);
    }

    private function getImageModel(array $dbEntity, string $key = 'image'): ?Image
    {
        $imageMapper = $this->mapperFactory->getImageMapper();

        // Image inheritance. If the current entity does not have an image
        // attached to it, look to see if its parent has an image, and use that.
        // Keep going up the ancestry chain till an image is found
        $currentItem = $dbEntity;
        while ($currentItem) {
            // If the current Entity has an image then use that!
            if (isset($currentItem[$key])) {
                return $imageMapper->getDomainModel($currentItem[$key]);
            }

            // Otherwise set the current Entity to the parent
            $currentItem = $currentItem['parent'] ?? null;
        }

        // Could not find any Entity Images in the hierarchy, try the
        // MasterBrand image.
        // This should also attempt inheritance where if the current entity
        // has no MasterBrand image then it should work up the ancestry chain
        // till an image is found.
        $currentItem = $dbEntity;
        while ($currentItem) {
            // If the current Entity's MasterBrand has an image then use that!
            if (isset($currentItem['masterBrand']['image'])) {
                return $imageMapper->getDomainModel($currentItem['masterBrand']['image']);
            }

            // Otherwise set the current Entity to the parent
            $currentItem = $currentItem['parent'] ?? null;
        }

        // Could not find any entity image in the masterbrand, go up to the network
        $currentItem = $dbEntity;
        while ($currentItem) {
            // If the current Programme's network has an image then use that!
            if (isset($currentItem['masterBrand']['network']['image'])) {
                return $imageMapper->getDomainModel($currentItem['masterBrand']['network']['image']);
            }

            // Otherwise set the current Entity to the parent
            $currentItem = $currentItem['parent'] ?? null;
        }

        // Couldn't find anything in the masterbrand, so use the default Image
        return $imageMapper->getDefaultImage();
    }

    private function getMasterBrandModel(array $dbEntity, string $key = 'masterBrand'): ?MasterBrand
    {
        // It is possible to have no MasterBrand, where the key does
        // exist but is set to null. We'll only say it's Unfetched
        // if the key doesn't exist at all.
        if (!array_key_exists($key, $dbEntity)) {
            return new UnfetchedMasterBrand();
        }

        if (is_null($dbEntity[$key])) {
            return null;
        }

        return $this->mapperFactory->getMasterBrandMapper()->getDomainModel($dbEntity[$key]);
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

    private function getSynopses($dbEntity): Synopses
    {
        return new Synopses(
            $dbEntity['shortSynopsis'],
            $dbEntity['mediumSynopsis'],
            $dbEntity['longSynopsis']
        );
    }

    private function entityIsA(string $string, array $dbEntity): bool
    {
        if (!isset($dbEntity['type'])) {
            return false;
        }

        if ($string == Group::class && in_array($dbEntity['type'], ['collection', 'franchise', 'gallery', 'season'])) {
            return true;
        }

        if ($string == Programme::class && in_array($dbEntity['type'], ['brand', 'clip', 'episode', 'series'])) {
            return true;
        }

        return false;
    }
}
