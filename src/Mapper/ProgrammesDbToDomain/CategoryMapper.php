<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Category;
use BBC\ProgrammesPagesService\Domain\Entity\Format;
use BBC\ProgrammesPagesService\Domain\Entity\Genre;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedGenre;
use BBC\ProgrammesPagesService\Mapper\Traits\AncestryArrayTrait;
use InvalidArgumentException;

class CategoryMapper extends AbstractMapper
{
    use AncestryArrayTrait;

    private $cache = [];

    public function getCacheKey(array $dbCategory): string
    {
        return $this->buildCacheKey(
            $dbCategory,
            'id',
            ['parent' => 'Category'],
            ['children' => 'Category']
        );
    }

    public function getDomainModel(array $dbCategory): Category
    {
        $cacheKey = $this->getCacheKey($dbCategory);

        if (!isset($this->cache[$cacheKey])) {
            $this->cache[$cacheKey] = $this->getModel($dbCategory);
        }

        return $this->cache[$cacheKey];
    }

    private function getModel(array $dbCategory): Category
    {
        if ($dbCategory['type'] == 'genre') {
            return $this->getGenreDomainModel($dbCategory);
        } elseif ($dbCategory['type'] == 'format') {
            return $this->getFormatDomainModel($dbCategory);
        }

        throw new InvalidArgumentException('Could not build domain model for unknown category type "' . ($dbCategory['type'] ?? '') . '"');
    }

    private function getFormatDomainModel(array $dbCategory): Format
    {
        return new Format(
            $this->getAncestryArray($dbCategory),
            $dbCategory['pipId'],
            $dbCategory['title'],
            $dbCategory['urlKey']
        );
    }

    private function getGenreDomainModel(array $dbCategory): Genre
    {
        return new Genre(
            $this->getAncestryArray($dbCategory),
            $dbCategory['pipId'],
            $dbCategory['title'],
            $dbCategory['urlKey'],
            $this->getGenreParentModel($dbCategory, 'parent'),
            $this->getGenreChildrenModel($dbCategory, 'children')
        );
    }

    private function getGenreParentModel(array $dbCategory, string $key = 'parent'): ?Genre
    {
        // It is possible to have no parent, where the key does
        // exist but is set to null. We'll only say it's Unfetched
        // if the key doesn't exist at all.
        if (!array_key_exists($key, $dbCategory)) {
            return new UnfetchedGenre();
        }

        if (is_null($dbCategory[$key])) {
            return null;
        }

        return $this->getDomainModel($dbCategory[$key]);
    }

    private function getGenreChildrenModel(array $dbCategory, string $key = 'children'): ?array
    {
        if (!array_key_exists($key, $dbCategory) || $dbCategory[$key] === null) {
            return null;
        }

        $children = [];
        foreach ($dbCategory[$key] as $child) {
            // Circular references are fun for the whole family
            // (except grandma and grandpa)
            $child['parent'] = $dbCategory;
            unset($child['parent']['children']);
            $children[] = $this->getDomainModel($child);
        }

        return $children;
    }
}
