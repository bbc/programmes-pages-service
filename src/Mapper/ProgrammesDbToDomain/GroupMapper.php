<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Collection;
use BBC\ProgrammesPagesService\Domain\Entity\Franchise;
use BBC\ProgrammesPagesService\Domain\Entity\Gallery;
use BBC\ProgrammesPagesService\Domain\Entity\Group;
use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\Entity\MasterBrand;
use BBC\ProgrammesPagesService\Domain\Entity\Options;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Domain\Entity\Season;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedMasterBrand;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedOptions;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedProgramme;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;
use InvalidArgumentException;

class GroupMapper extends AbstractMapper
{
    private $cache = [];

    public function getCacheKey(array $dbGroup): string
    {
        return $this->buildCacheKey($dbGroup, 'id', [
            'image' => 'Image',
            'parent' => 'Programme',
            'masterBrand' => 'MasterBrand',
        ]);
    }

    public function getDomainModel(array $dbGroup): Group
    {
        $cacheKey = $this->getCacheKey($dbGroup);

        if (!isset($this->cache[$cacheKey])) {
            $this->cache[$cacheKey] = $this->getModel($dbGroup);
        }

        return $this->cache[$cacheKey];
    }

    public function getModel(array $dbGroup): Group
    {
        if (isset($dbGroup['type'])) {
            if ($dbGroup['type'] == 'collection') {
                return $this->getCollectionModel($dbGroup);
            }
            if ($dbGroup['type'] == 'gallery') {
                return $this->getGalleryModel($dbGroup);
            }
            if ($dbGroup['type'] == 'season') {
                return $this->getSeasonModel($dbGroup);
            }
            if ($dbGroup['type'] == 'franchise') {
                return $this->getFranchiseModel($dbGroup);
            }
        }

        throw new InvalidArgumentException('Could not build domain model for unknown group type "' . ($dbGroup['type'] ?? '') . '"');
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

    private function getAncestryArray(array $dbGroup, string $key = 'ancestry'): array
    {
        // ancestry contains a string of all IDs including the current one with
        // a trailing comma at the end (which makes it an empty item when exploding)
        // Thus we want an array of all but the final item (which is null)
        $ancestors = explode(',', $dbGroup[$key], -1) ?? [];
        return array_map(function ($a) {
            return (int) $a;
        }, $ancestors);
    }

    private function getOptionsModel(array $dbGroup, string $key = 'options'): Options
    {
        // ensure the full hierarchy has been fetched.
        // Options are only valid if we got everything.
        if (!$this->hasFetchedFullHierarchy($dbGroup)) {
            return new UnfetchedOptions();
        }

        // build for current dbGroup the tree of options
        $optionsTree = $this->getOptionsTree($dbGroup, $key);
        // get final options to be applied on group from tree options hierarchy
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

    private function getOptionsTree(array $dbGroup, string $keyWithOptions, array $optionsTree = []): array
    {
        // Recursive up the group hierarchy, then to the network above that
        $optionsTree[] = $dbGroup[$keyWithOptions] ?? [];
        if (isset($dbGroup['parent'])) {
            $optionsTree = $this->getOptionsTree($dbGroup['parent'], $keyWithOptions, $optionsTree);
        } elseif (isset($dbGroup['masterBrand']['network'])) {
            $optionsTree = $this->getOptionsTree($dbGroup['masterBrand']['network'], $keyWithOptions, $optionsTree);
        }
        return $optionsTree;
    }

    private function getParentModel(array $dbGroup, string $key = 'parent'): ?Programme
    {
        // It is possible to have no parent, where the key does
        // exist but is set to null. We'll only say it's Unfetched
        // if the key doesn't exist at all.
        if (!array_key_exists($key, $dbGroup)) {
            return new UnfetchedProgramme();
        }

        if (is_null($dbGroup[$key])) {
            return null;
        }

        return $this->mapperFactory->getProgrammeMapper()->getDomainModel($dbGroup[$key]);
    }

    private function getImageModel(array $dbGroup, string $key = 'image'): ?Image
    {
        $imageMapper = $this->mapperFactory->getImageMapper();

        // Image inheritance. If the current group does not have an image
        // attached to it, look to see if its parent has an image, and use that.
        // Keep going up the ancestry chain till an image is found
        $currentItem = $dbGroup;
        while ($currentItem) {
            // If the current Group has an image then use that!
            if (isset($currentItem[$key])) {
                return $imageMapper->getDomainModel($currentItem[$key]);
            }

            // Otherwise set the current Group to the parent
            $currentItem = $currentItem['parent'] ?? null;
        }

        // Could not find any Group Images in the hierarchy, try the
        // MasterBrand image.
        // This should also attempt inheritance where if the current group
        // has no MasterBrand image then it should work up the ancestry chain
        // till an image is found.
        $currentItem = $dbGroup;
        while ($currentItem) {
            // If the current Group's MasterBrand has an image then use that!
            if (isset($currentItem['masterBrand']['image'])) {
                return $imageMapper->getDomainModel($currentItem['masterBrand']['image']);
            }

            // Otherwise set the current Group to the parent
            $currentItem = $currentItem['parent'] ?? null;
        }

        // Could not find any group image in the masterbrand, go up to the network
        $currentItem = $dbGroup;
        while ($currentItem) {
            // If the current Programme's network has an image then use that!
            if (isset($currentItem['masterBrand']['network']['image'])) {
                return $imageMapper->getDomainModel($currentItem['masterBrand']['network']['image']);
            }

            // Otherwise set the current Group to the parent
            $currentItem = $currentItem['parent'] ?? null;
        }

        // Couldn't find anything in the masterbrand, so use the default Image
        return $imageMapper->getDefaultImage();
    }

    private function getMasterBrandModel(array $dbGroup, string $key = 'masterBrand'): ?MasterBrand
    {
        // It is possible to have no MasterBrand, where the key does
        // exist but is set to null. We'll only say it's Unfetched
        // if the key doesn't exist at all.
        if (!array_key_exists($key, $dbGroup)) {
            return new UnfetchedMasterBrand();
        }

        if (is_null($dbGroup[$key])) {
            return null;
        }

        return $this->mapperFactory->getMasterBrandMapper()->getDomainModel($dbGroup[$key]);
    }

    private function getSynopses($dbGroup): Synopses
    {
        return new Synopses(
            $dbGroup['shortSynopsis'],
            $dbGroup['mediumSynopsis'],
            $dbGroup['longSynopsis']
        );
    }
}
