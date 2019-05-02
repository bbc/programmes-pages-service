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
use BBC\ProgrammesPagesService\Mapper\Traits\AncestryArrayTrait;
use BBC\ProgrammesPagesService\Mapper\Traits\SynopsesTrait;
use InvalidArgumentException;

class CoreEntityMapper extends AbstractMapper
{
    use SynopsesTrait;
    use AncestryArrayTrait;

    private const PROGRAMME_TYPES = ['brand', 'clip', 'episode', 'series'];
    private const GROUP_TYPES = ['collection', 'franchise', 'gallery', 'season'];

    private $cache = [];

    /**
     * For building cache keys for other mappers in a generic way
     *
     * @param array $dbEntity
     * @return string
     */
    public function getCacheKey(array $dbEntity): string
    {
        return $this->buildCacheKey($dbEntity, 'id', [
            'image' => 'Image',
            'parent' => 'CoreEntity',
            'masterBrand' => 'MasterBrand',
        ], [
            // categories are only used by Programmes
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
        if ($this->isProgramme($dbEntity)) {
            return $this->getDomainModelForProgramme($dbEntity);
        }
        if ($this->isGroup($dbEntity)) {
            return $this->getDomainModelForGroup($dbEntity);
        }
        throw new InvalidArgumentException('Unrecognized Core Entity');
    }

    public function getDomainModelForGroup(array $dbEntity): Group
    {
        if (!$this->isGroup($dbEntity)) {
            throw new InvalidArgumentException('Could not build domain model for unknown group type "' . ($dbEntity['type'] ?? '') . '"');
        }

        $cacheKey = $this->getCacheKey($dbEntity);

        if (!isset($this->cache[$cacheKey])) {
            if ($dbEntity['type'] == 'collection') {
                $this->cache[$cacheKey] = $this->getCollectionModel($dbEntity);
            } elseif ($dbEntity['type'] == 'gallery') {
                $this->cache[$cacheKey] = $this->getGalleryModel($dbEntity);
            } elseif ($dbEntity['type'] == 'season') {
                $this->cache[$cacheKey] = $this->getSeasonModel($dbEntity);
            } elseif ($dbEntity['type'] == 'franchise') {
                $this->cache[$cacheKey] = $this->getFranchiseModel($dbEntity);
            }
        }

        return $this->cache[$cacheKey];
    }

    public function getDomainModelForProgramme(array $dbEntity): Programme
    {
        if (!$this->isProgramme($dbEntity)) {
            throw new InvalidArgumentException('Could not build domain model for unknown programme type "' . ($dbEntity['type'] ?? '') . '"');
        }

        $cacheKey = $this->getCacheKey($dbEntity);

        if (!isset($this->cache[$cacheKey])) {
            if ($dbEntity['type'] == 'brand') {
                $this->cache[$cacheKey] = $this->getBrandModel($dbEntity);
            } elseif ($dbEntity['type'] == 'series') {
                $this->cache[$cacheKey] = $this->getSeriesModel($dbEntity);
            } elseif ($dbEntity['type'] == 'episode') {
                $this->cache[$cacheKey] = $this->getEpisodeModel($dbEntity);
            } elseif ($dbEntity['type'] == 'clip') {
                $this->cache[$cacheKey] = $this->getClipModel($dbEntity);
            }
        }

        return $this->cache[$cacheKey];
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
            $dbProgramme['aggregatedGalleriesCount'],
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
            $dbProgramme['aggregatedGalleriesCount'],
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
            $dbProgramme['aggregatedGalleriesCount'],
            $dbProgramme['embeddable'],
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
            $this->castDateTime($dbProgramme['streamableUntil']),
            $dbProgramme['downloadableMediaSets'] ?? []
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
            $dbProgramme['aggregatedGalleriesCount'],
            $dbProgramme['embeddable'],
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
            $this->castDateTime($dbProgramme['streamableUntil']),
            $dbProgramme['downloadableMediaSets'] ?? []
        );
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

    private function getImageModel(array $dbEntity, string $key = 'image'): Image
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

        // CoreEntities may not have MasterBrands.
        // Most systems will want to look up the parent hierarchy to see if
        // there is a MasterBrand we can attach to the current item. However
        // some legacy APIs we still need to maintain (e.g. Clifton) do not
        // expose the inherited MasterBrand.
        if (!$this->mapperFactory->getOption('core_entity_inherit_master_brand')) {
            return $this->getMasterBrandModelWithoutInheritance($dbEntity[$key]);
        }

        $masterBrandMapper = $this->mapperFactory->getMasterBrandMapper();

        // MasterBrand inheritance. If the current entity does not have a
        // masterbrand attached to it, look to see if its parent has a
        // masterbrand, and use that. Keep going up the ancestry chain till a
        // masterbrand is found
        $currentItem = $dbEntity;
        while ($currentItem) {
            // If the current Entity has a masterbrand then use that!
            if (isset($currentItem[$key])) {
                return $masterBrandMapper->getDomainModel($currentItem[$key]);
            }

            // Otherwise set the current Entity to the parent
            $currentItem = $currentItem['parent'] ?? null;
        }

        return null;
    }

    private function getMasterBrandModelWithoutInheritance(?array $dbMasterBrand): ?MasterBrand
    {
        if (is_null($dbMasterBrand)) {
            return null;
        }

        return $this->mapperFactory->getMasterBrandMapper()->getDomainModel($dbMasterBrand);
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

    private function isProgramme(array $dbEntity): bool
    {
        return isset($dbEntity['type']) && in_array($dbEntity['type'], self::PROGRAMME_TYPES);
    }

    private function isGroup(array $dbEntity): bool
    {
        return isset($dbEntity['type']) && in_array($dbEntity['type'], self::GROUP_TYPES);
    }
}
