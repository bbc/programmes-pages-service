<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Category;
use BBC\ProgrammesPagesService\Domain\Entity\Format;
use BBC\ProgrammesPagesService\Domain\Entity\Genre;
use InvalidArgumentException;

class CategoryMapper extends AbstractMapper
{
    private $cache = [];

    public function getCacheKey(array $dbCategory): string
    {
        return $this->buildCacheKey($dbCategory, 'id', [
            'parent' => 'Category',
        ]);
    }

    public function getDomainModel(array $dbCategory): Category
    {
        $cacheKey = $this->getCacheKey($dbCategory);

        if (!isset($this->cache[$cacheKey])) {
            $this->cache[$cacheKey] = $this->getModel($dbCategory);
        }

        return $this->cache[$cacheKey];
    }

    public function getModel(array $dbCategory): Category
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
            $this->getGenreParentModel($dbCategory, 'parent')
        );
    }

    private function getGenreParentModel($dbCategory, $key = 'parent')
    {
        if (!isset($dbCategory[$key])) {
            return null;
        }

        return $this->getDomainModel($dbCategory[$key]);
    }

    private function getAncestryArray($dbCategory, $key = 'ancestry')
    {
        // ancestry contains a string of all IDs including the current one with
        // a trailing comma at the end (which makes it an empty item when exploding)
        // Thus we want an array of all but the final item (which is null)
        $ancestors = explode(',', $dbCategory[$key], -1) ?? [];
        return array_map(function ($a) {
            return (int) $a;
        }, $ancestors);
    }
}
